FROM infracamp/kickstart-flavor-arm32v7-php7:testing

ENV DEV_CONTAINER_NAME="raspiki"

ADD / /opt
RUN ["bash", "-c",  "chown -R user /opt"]
RUN ["/kickstart/flavorkit/scripts/start.sh", "build"]

ENTRYPOINT ["/kickstart/flavorkit/scripts/start.sh", "standalone"]
