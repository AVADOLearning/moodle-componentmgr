require 'rspec'
require 'serverspec'

if (/cygwin|mswin|mingw|bccwin|wince|emx/ =~ RUBY_PLATFORM).nil?
  set :backend, :exec
else
  set :backend, :cmd
  set :os, family: 'windows'
end

RSpec.configure do |config|
  config.expect_with :rspec do |c|
    c.syntax = [:should]
  end
end
