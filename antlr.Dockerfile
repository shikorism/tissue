FROM adoptopenjdk:11-jre-hotspot

RUN curl -Lo /usr/local/lib/antlr-4.10.1-complete.jar https://www.antlr.org/download/antlr-4.10.1-complete.jar

RUN { \
        echo '#!/bin/sh'; \
        echo 'java -Xmx500M -cp "/usr/local/lib/antlr-4.10.1-complete.jar:\$CLASSPATH" org.antlr.v4.Tool "$@"'; \
    } > /usr/local/bin/antlr4 \
    && chmod +x /usr/local/bin/antlr4

CMD ["/usr/local/bin/antlr4"]
