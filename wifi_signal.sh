#!/bin/sh

PATH=/usr/bin:/usr/sbin:/bin:/sbin
export PATH

INTERFACE=wlan1

do_ () {
    OUTPUT=$(/sbin/iw dev $INTERFACE station dump)
    echo "signal.value $(echo "$OUTPUT" | grep 'signal avg' | cut -d':' -f 2 | cut -d' ' -f 1 | xargs)"
}

do_config () {
    cat <<'EOF'
graph_title Freifunk Signal
graph_vlabel channel time
graph_category freifunk
signal.label signal
signal.type GAUGE
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