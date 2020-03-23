# OAuth-Learning
This repository mainly focuses on learning OAuth protocol by referring to the guide at OAuth.com.
Start learning.

# Github Ouath

## First tried with Php backend server
This is under Github Oauth folder

## Second we are trying to build
We are also trying to make an attempt to build a client application for implicit flow work using Maven project inside Implicit flow folder   

To generate project:
mvn archetype:generate -DgroupId=com.test -DartifactId=test -DarchetypeArtifactId=maven-archetype-webapp -DinteractiveMode=false

To run project:
sudo mvn -Dmaven.tomcat.port=80 -Dmaven.tomcat.path=/ tomcat:run
