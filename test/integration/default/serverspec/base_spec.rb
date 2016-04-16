require 'spec_helper'

describe 'temporary file cleanup' do
  describe file('/tmp') do
    it { should be_a_directory }

    it 'should not contain Component Manager temporary directories' do
      Dir.glob('/tmp/componentmgr-*').should eq []
    end
  end
end
