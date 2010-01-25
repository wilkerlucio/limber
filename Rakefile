# Copyright 2009-2010 Limber Framework
# 
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
# 
#     http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License. 

require 'rake'

desc "Update copyright year of files"
task :update_copyright do
  initial_year = '2009'
  current_year = DateTime.now.year
  copyright_pattern = / \* Copyright (.*) Limber Framework/
  
  Dir["**/*.php"].each do |file|
    content = File.read(file)
    content.gsub! copyright_pattern, " * Copyright #{initial_year}-#{current_year} Limber Framework"
    
    File.open file, "wb" do |f|
      f << content
    end
  end
end