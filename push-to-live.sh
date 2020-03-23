#!/usr/bin/env sh
git tag `date +'%Y-%m-%d_%H_%M_%S'`
git push --tags
