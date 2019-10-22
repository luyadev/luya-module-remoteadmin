# LUYA REMOTE ADMIN UPGRADE

This document will help you upgrading from a LUYA remote module version into another. 

## from 1.x to 2.0

+ This release contains the new migrations which are required for the user and file table. Therefore make sure to run the `./vendor/bin/luya migrate` command after `composer update`.