#!/bin/sh

PATH=/usr/bin:/usr/sbin:/bin:/sbin
export PATH

INTERFACE=wlan1

do_ () {
    OUTPUT=$(/sbin/iw dev $INTERFACE station dump)
    echo "rxbytes.value $(echo "$OUTPUT" | grep 'rx bytes' | cut -d':' -f 2 | xargs)"
    echo "txbytes.value $(echo "$OUTPUT" | grep 'tx bytes' | cut -d':' -f 2 | xargs)"
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