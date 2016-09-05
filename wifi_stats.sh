#!/bin/sh

PATH=/usr/bin:/usr/sbin:/bin:/sbin
export PATH

INTERFACE=wlan1

do_ () {
    OUTPUT=$(/sbin/iw dev $INTERFACE station dump)
    echo "rxbytes.value $(echo "$OUTPUT" | grep 'rx bytes' | cut -d':' -f 2 | xargs)"
    echo "rxpackets.value $(echo "$OUTPUT" | grep 'rx packets' | cut -d':' -f 2 | xargs)"
    echo "txbytes.value $(echo "$OUTPUT" | grep 'tx bytes' | cut -d':' -f 2 | xargs)"
    echo "txpackets.value $(echo "$OUTPUT" | grep 'tx packets' | cut -d':' -f 2 | xargs)"
    echo "signal.value $(echo "$OUTPUT" | grep 'signal avg' | cut -d':' -f 2 | cut -d' ' -f 1 | xargs)"
    echo "txbitrate.value $(echo "$OUTPUT" | grep 'tx bitrate' | cut -d':' -f 2 | cut -d' ' -f 1 | xargs)"
    echo "rxbitrate.value $(echo "$OUTPUT" | grep 'rx bitrate' | cut -d':' -f 2 | cut -d' ' -f 1 | xargs)"
}

do_config () {
    cat <<'EOF'
graph_title Freifunk Bytes
graph_vlabel channel time
graph_category freifunk
rxbytes.label rx
rxbytes.type GAUGE
txbytes.label tx
txbytes.type GAUGE
graph_title Freifunk Packets
graph_vlabel channel time
graph_category freifunk
rxpackets.label rx
rxpackets.type GAUGE
txpackets.label tx
txpackets.type GAUGE
graph_title Freifunk Bitrate
graph_vlabel channel time
graph_category freifunk
rxbitrate.label rx
rxbitrate.type GAUGE
txbitrate.label tx
txbitrate.type GAUGE
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