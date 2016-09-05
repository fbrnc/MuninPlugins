#!/bin/sh

PATH=/usr/bin:/usr/sbin:/bin:/sbin
export PATH

INTERFACE=wlan1

do_ () {
    OUTPUT=$(/sbin/iw dev $INTERFACE station dump)
    echo "txbitrate.value $(echo "$OUTPUT" | grep 'tx bitrate' | cut -d':' -f 2 | cut -d' ' -f 1 | xargs)"
    echo "rxbitrate.value $(echo "$OUTPUT" | grep 'rx bitrate' | cut -d':' -f 2 | cut -d' ' -f 1 | xargs)"
}

do_config () {
    cat <<'EOF'
graph_title Freifunk Bitrate
graph_vlabel channel time
graph_category freifunk
rxbitrate.label rx
rxbitrate.type GAUGE
txbitrate.label tx
txbitrate.type GAUGE
EOF
}

do_autoconf () {
    echo yes
    exit 0
}

case $1 in
        ''|config|autoconf)
                eval do_$1;;
esac