#!/bin/sh

PATH=/usr/bin:/usr/sbin:/bin:/sbin
export PATH

INTERFACE=wlan1

do_ () {
    OUTPUT=$(/sbin/iw dev $INTERFACE station dump)
    echo "rxpackets.value $(echo "$OUTPUT" | grep 'rx packets' | cut -d':' -f 2 | xargs)"
    echo "txpackets.value $(echo "$OUTPUT" | grep 'tx packets' | cut -d':' -f 2 | xargs)"
}

do_config () {
    cat <<'EOF'
graph_title Freifunk Packets
graph_vlabel channel time
graph_category freifunk
rxpackets.label rx
rxpackets.type GAUGE
txpackets.label tx
txpackets.type GAUGE
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