#!/bin/bash

xrdb -merge "${HOME}/.Xresources"

setxkbmap -option '' -option kpdl:dot -option caps:swapescape no

feh ~/water_fantasy_art_artwork_huts_2560x1440_.jpg --bg-fill
sh ~/scripts/compton

numlockx &
bmpanel2 &

# default is openbox
session=${1:-openbox}

case $session in
    awesome       ) exec awesome;;
    bspwm         ) exec bspwm;;
    catwm         ) exec catwm;;
    cinnamon      ) exec cinnamon-session;;
    dwm           ) exec dwm;;
    enlightenment ) exec enlightenment_start;;
    ede           ) exec startede;;
    evilwm        ) exec evilwm;;
    fluxbox       ) exec startfluxbox;;
    gnome         ) exec gnome-session;;
    gnome-classic ) exec gnome-session --session=gnome-classic;;
    i3            ) exec i3;;
    icewm         ) exec icewm-session;;
    jwm           ) exec jwm;;
    kde           ) exec startkde;;
    mate          ) exec mate-session;;
    monster       ) exec monsterwm;;
    notion        ) exec notion;;
    openbox       ) exec openbox;;
    #openbox       ) exec openbox-session;;
    swm           ) exec swm;;
    unity         ) exec unity;;
    xfce          ) exec startxfce4;;
    xmonad        ) exec xmonad;;
    # No known session, try to run it as command
    *) exec $1;;
esac

