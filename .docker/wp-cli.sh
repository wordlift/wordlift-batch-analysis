#!/usr/bin/env bash

docker run --rm --volumes-from docker_batch-analysis-wordpress_1 --network container:docker_batch-analysis-wordpress_1 --user 33:33 wordpress:cli "$@"
