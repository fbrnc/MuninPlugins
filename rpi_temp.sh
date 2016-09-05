#!/bin/bash

PATH=/usr/bin:/usr/sbin:/bin:/sbin
export PATH

do_ () {
    gpu=$(/opt/vc/bin/vcgencmd measure_temp | cut -d'=' -f 2 | cut -d\' -f 1)
    echo "gpu.value $gpu"
    cpu=$(</sys/class/thermal/thermal_zone0/temp)
    echo "cpu.value $((cpu/1000))"
}

do_config () {
    cat <<'EOF'
graph_title RPI
graph_vlabel Temperature
graph_category misc
cpu.label CPU
cpu.type GAUGE
gpu.label GPU
gpu.type GAUGE
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