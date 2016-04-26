#!/usr/bin/env bash

. 'test/travis/lib.sh'

strict_enable

DOCKER_DEBUG="${DOCKER_DEBUG:-false}"

travis_fold start env.setup
    travis_section 'Exporting UML environment variables'

    SLIRP_HOST="$(ip addr | awk '/scope global/ {print $2; exit}' | cut -d/ -f1)"
    SLIRP_MIN_PORT='2375'
    SLIRP_MAX_PORT='2400'
    SLIRP_PORTS="$(seq "${SLIRP_MIN_PORT}" "${SLIRP_MAX_PORT}")"
    DOCKER_HOST="tcp://${SLIRP_HOST}:${SLIRP_MIN_PORT}"
    DOCKER_PORT_RANGE="$((SLIRP_MIN_PORT+1)):${SLIRP_MAX_PORT}"

    export SLIRP_HOST DOCKER_HOST DOCKER_PORT_RANGE SLIRP_PORTS

    travis_dump_var SLIRP_HOST
    travis_dump_var SLIRP_MIN_PORT
    travis_dump_var SLIRP_MAX_PORT
    travis_dump_var SLIRP_PORTS
    travis_dump_var DOCKER_HOST
    travis_dump_var DOCKER_PORT_RANGE
travis_fold end env.setup

travis_fold start curl.install
    travis_section 'Installing cURL'

    sudo apt-get update
    sudo apt-get install -y ca-certificates curl
travis_fold end curl.install

travis_fold start docker.repository
    travis_section 'Installing Docker APT repository'

    curl https://get.docker.com/gpg | sudo apt-key add -
    echo 'deb https://get.docker.io/ubuntu docker main' | sudo tee /etc/apt/sources.list.d/docker.list
travis_fold end docker.repository

travis_fold start apparmor.reinstall
    travis_section 'Reinstalling AppArmor (Could not open 'tunables/global')'

    sudo apt-get -o Dpkg::Options::=--force-confnew -o Dpkg::Options::=--force-confmiss --reinstall install apparmor
travis_fold end apparmor.reinstall

travis_fold start apt.policy.setup
    travis_section 'Preventing APT from starting any service'

    echo exit 101 | sudo tee /usr/sbin/policy-rc.d
    sudo chmod +x /usr/sbin/policy-rc.d
travis_fold end apt.policy.setup

travis_fold start docker.install
    travis_section 'Installing Docker'

    sudo apt-get update
    sudo apt-get -y install lxc lxc-docker slirp

    sudo usermod -aG docker "${USER}"
    sudo service docker stop || true
travis_fold end docker.install

travis_fold start uml.download
    travis_section 'Downloading User Mode Linux scripts'
    if ! [ -e sekexe ]; then
        travis_retry git clone git://github.com/cptactionhank/sekexe
    fi
travis_fold end uml.download

travis_fold start docker.start
    travis_section 'Starting Docker Engine'

    cat > run-uml.sh << EOF
sekexe/run "
    echo ${SLIRP_MIN_PORT} ${SLIRP_MAX_PORT} > /proc/sys/net/ipv4/ip_local_port_range && \\
    docker daemon -H tcp://0.0.0.0:${SLIRP_MIN_PORT}
"
EOF
    chmod +x run-uml.sh

    if [ "${DOCKER_DEBUG}" = 'true' ]; then
        ./run-uml.sh 2>&1 | tee -a docker_daemon.log &
    else
        ./run-uml.sh &> docker_daemon.log &
    fi
travis_fold end docker.start

travis_fold start docker.wait
    travis_section 'Waiting for Docker to start'

    TIME=0
    while ! docker info &> /dev/null; do
        [ "${TIME}" -gt 60 ] && exit 1
        echo -n .
        sleep 1
        TIME="$((TIME+1))"
    done
travis_fold end docker.wait

travis_section 'Complete'

strict_disable
