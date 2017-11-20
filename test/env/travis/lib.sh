#!/usr/bin/env bash

# Easily toggle "strict mode"
strict_enable() {
    set -euo pipefail
    IFS=$'\n\t'
}
strict_disable() {
    set +euo pipefail
    IFS=$' \t\n'
}

# ANSI escape sequences
ANSI_CLEAR="${ANSI_CLEAR:-\e[0K}"
ANSI_RESET="${ANSI_RESET:-\e[0m}"
ANSI_RED="${ANSI_RED:-\e[31;1m}"
ANSI_YELLOW="${ANSI_YELLOW:-\e[33;1m}"

# Patch in support for folds if not already available
# https://blog.travis-ci.com/2013-05-22-improving-build-visibility-log-folds/
if ! type travis_fold &>/dev/null; then
    travis_fold() {
        local action="$1"
        local name="$2"

        echo -en "travis_fold:${action}:${name}\r${ANSI_CLEAR}"
    }
fi

# Patch in retry, for resiliency
# https://blog.travis-ci.com/2013-05-20-network-timeouts-build-retries/
if ! type travis_retry &>/dev/null; then
    travis_retry() {
        local result=0
        local count=1

        while [ $count -le 3 ]; do
            [ $result -ne 0 ] && {
                echo -e "\n${ANSI_RED}The command \"${@}\" failed. Retrying, ${count} of 3.${ANSI_RESET}\n" >&2
            }

            "$@"
            result=$?

            [ $result -eq 0 ] && break
            count=$(($count + 1))
            sleep 1
        done

        [ $count -gt 3 ] && {
            echo -e "\n${ANSI_RED}The command \"$@\" failed 3 times.${ANSI_RESET}\n" >&2
        }

        return $result
    }
fi

# Pretty section headings
if ! type travis_section &>/dev/null; then
    travis_section() {
        echo -e "${ANSI_YELLOW}${*}${ANSI_RESET}"
    }
fi

# Variable dump
if ! type travis_dump_var &>/dev/null; then
    travis_dump_var() {
        local name="$1"
        local value="${!1}"
        echo "${name}=${value}"
    }
fi
