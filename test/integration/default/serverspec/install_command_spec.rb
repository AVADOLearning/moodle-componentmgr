require 'spec_helper'

describe 'install command' do
  describe file('/tmp/install/moodle/componentmgr.lock.json') do
    it { should be_a_file }

    its(:content) { should contain /"componentName": *"cachestore_redis"/ }
    its(:content) { should contain /"finalVersion": *"[a-f0-9]{40}"/ }

    its(:content) { should contain /"componentName": *"local_cpd"/ }
    its(:content) { should contain /"md5Checksum": *"[a-f0-9]{32}"/ }
  end

  describe file('/tmp/install/moodle/cache/stores/redis/version.php') do
    it { should be_a_file }
  end

  describe file('/tmp/install/moodle/local/cpd/version.php') do
    it { should be_a_file }
    its(:content) { should contain /\$plugin->release *= *('|")0\.4\.0('|");/ }
  end
end
