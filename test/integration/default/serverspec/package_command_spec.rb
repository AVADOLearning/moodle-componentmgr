require 'spec_helper'

describe 'package command' do
  describe file('/tmp/package/componentmgr.lock.json') do
    it { should be_a_file }

    its(:content) { should contain /"componentName": *"cachestore_redis"/ }
    its(:content) { should contain /"finalVersion": *"[a-f0-9]{40}"/ }

    its(:content) { should contain /"componentName": *"local_cpd"/ }
    its(:content) { should contain /"md5Checksum": *"[a-f0-9]{32}"/ }

    its(:content) { should contain /"componentName": *"local_componentmgrtest"/ }
    its(:content) { should contain /"finalVersion": *null/ }
  end

  describe file('/tmp/package/version.php') do
    it { should be_a_file }
    its(:content) { should contain /\$release *= *('|")3\.1\.[0-9]+\+? *\(Build: [0-9]+\)('|");/ }
  end

  describe file('/tmp/package/cache/stores/redis/version.php') do
    it { should be_a_file }
  end

  describe file('/tmp/package/local/cpd/version.php') do
    it { should be_a_file }
    its(:content) { should contain /\$plugin->release *= *('|")0\.4\.0('|");/ }
  end

  describe file('/tmp/package/local/componentmgrtest/version.php') do
    it { should be_a_file }
  end

  describe file('/tmp/package/local/componentmgrtest/hello-world') do
      it { should be_a_file }
    end
end
