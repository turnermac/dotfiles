-- Joe's (krasnayarsk) xmonad.hs file.

import System.IO
import System.Exit
import XMonad
import XMonad.Hooks.ManageHelpers
import XMonad.Hooks.ManageDocks
import XMonad.Hooks.SetWMName
import XMonad.Layout.Fullscreen
import XMonad.Layout.NoBorders
import XMonad.Layout.Spiral
import XMonad.Layout.Tabbed
import XMonad.Layout.ThreeColumns
import XMonad.Util.Run(spawnPipe)
import XMonad.Util.EZConfig(additionalKeys)
import qualified XMonad.StackSet as W
import qualified Data.Map        as M
import Graphics.X11.ExtraTypes.XF86
import XMonad.Layout.Spacing
import XMonad.Layout.PerWorkspace
import XMonad.Layout.IM
import XMonad.Layout.Grid
import XMonad.Actions.GridSelect
import Data.Ratio ((%))
import XMonad.Actions.CycleWS
import XMonad.Layout.Gaps
import XMonad.Layout.Circle

------------------------------------------------------------------------
-- Terminal
-- The preferred terminal program, which is used in a binding below and by
-- certain contrib modules.
--
myTerminal = "/usr/bin/urxvt"
------------------------------------------------------------------------
-- Workspaces
-- The default number of workspaces (virtual screens) and their names.
--
myWorkspaces = ["1:code","2:games","3:web","4:word","5:aux","6:doc"] ++ map show [7..9]


------------------------------------------------------------------------
-- Window rules
-- Execute arbitrary actions and WindowSet manipulations when managing
-- a new window. You can use this to, for example, always float a
-- particular program, or have a client always appear on a particular
-- workspace.
--
-- To find the property name associated with a program, use
-- > xprop | grep WM_CLASS
-- and click on the client you're interested in.
--
-- To match on the WM_NAME, you can use 'title' in the same way that
-- 'className' and 'resource' are used below.
--
myManageHook = composeAll
    [ className =? "Zathura"			--> doShift "3:web"
    , className =? "Okular"			--> doShift "6:doc"
    , className =? "Firefox"			--> doShift "3:web"
    , className =? "Seamonkey"			--> doShift "6:doc"
    , className =? "Blender"			--> doShift "5:aux"
    , resource  =? "desktop_window"		--> doIgnore
    , className =? "Kcalc"			--> doFloat
    , className =? "Scid"			--> doShift "2:games"
    , className =? "Gimp"			--> doFloat
    , resource  =? "feh"			--> doFloat
    , className =? "Mpv"			--> doFloat
    , className =? "OpenOffice 4.1.1"		--> doShift "4:word"
    , className =? "Kopete"			--> doShift "3:web"
    , className =? "Xev"			--> doFloat
    , className =? "Mplayer"			--> doShift "1:code"
    , className =? "Dolphin"			--> doShift "games"
    , isFullscreen --> (doF W.focusDown <+> doFullFloat)]


------------------------------------------------------------------------
-- Layouts
-- You can specify and transform your layouts by modifying these values.
-- If you change layout bindings be sure to use 'mod-shift-space' after
-- restarting (with 'mod-q') to reset your layout state to the new
-- defaults, as xmonad preserves your old layout settings by default.
--
-- The available layouts.  Note that each layout is separated by |||,
-- which denotes layout choice.
--
myLayout = avoidStruts $ Circle |||
    spacing 7 (tabbed shrinkText tabConfig) |||
    Circle |||
    spacing 7 (Tall 1 (3/100) (1/2) |||
    Mirror (Tall 1 (3/100) (1/2)) |||
    Full |||
    spiral (6/7))


------------------------------------------------------------------------
-- Colors and borders

myNormalBorderColor  = "#646464"
myFocusedBorderColor = "#4D4D4D"

-- Colors for text and backgrounds of each tab when in "Tabbed" layout.
tabConfig = defaultTheme {
    activeBorderColor = "#7C7C7C",
    activeTextColor = "#CEFFAC",
    activeColor = "#000000",
    inactiveBorderColor = "#7C7C7C",
    inactiveTextColor = "#EEEEEE",
    inactiveColor = "#000000"
}

-- Color of current window title in xmobar.
xmobarTitleColor = "#FFB6B0"

-- Color of current workspace in xmobar.
xmobarCurrentWorkspaceColor = "#CEFFAC"

-- Width of the window border in pixels.
myBorderWidth = 2 


------------------------------------------------------------------------
-- Key bindings
--
-- modMask lets you specify which modkey you want to use. The default
-- is mod1Mask ("left alt").  You may also consider using mod3Mask
-- ("right alt"), which does not conflict with emacs keybindings. The
-- "windows key" is usually mod4Mask.
--
myModMask = mod4Mask

myKeys conf@(XConfig {XMonad.modMask = modMask}) = M.fromList $
  ----------------------------------------------------------------------
  -- Custom key bindings
  --

  -- Start a terminal.
  [ ((modMask .|. shiftMask, xK_Return),
       spawn $ XMonad.terminal conf)

  -- Launch dmenu (not via yeganesh.)
  -- Use this to launch programs without a key binding.
  , ((modMask, xK_p),
     spawn "exe=`dmenu_run -b -nb black -nf grey -sf yellow` && eval \"exec $exe\"")

  -- Take a screenshot in select mode.
  -- After pressing this key binding, click a window, or draw a rectangle with
  -- the mouse.
  , ((modMask .|. shiftMask, xK_p),
     spawn "select-screenshot")

  -- Take full screenshot in multi-head mode.
  -- That is, take a screenshot of everything you see.
  , ((modMask .|. controlMask .|. shiftMask, xK_p),
     spawn "screenshot")

  -- Start firefox in porn mode.
  , ((modMask .|. controlMask, xK_w), 
     spawn "/usr/bin/firefox --private ~/.startpages/twily/s/firefox/homepage-centered-iframe.html")

  -- Start firefox in normal mode.
  , ((modMask .|. controlMask, xK_r),
     spawn "firefox")

  -- Start Seamonky the TOR browser.
  , ((modMask .|. controlMask, xK_e),
     spawn "seamonkey duckduckgo.com")

  -- Start openoffice.
  , ((modMask .|. controlMask, xK_o),
      spawn "ooffice")
 
   -- Start calculator.
  , ((modMask .|. controlMask, xK_c),
      spawn "kcalc")

   -- Start chess programme.
  , ((modMask .|. controlMask, xK_s),
      spawn "scid")

   -- Start blender.
  , ((modMask .|. controlMask, xK_b),
      spawn "blender")
  
  -- Start Dolphin.
  , ((modMask .|. controlMask, xK_d),
      spawn "dolphin")

  -- Mute volume.
  , ((0, 0x1008FF12),
     spawn "amixer -q set Master toggle")

  -- Decrease volume.
  , ((0, 0x1008FF11),
     spawn "amixer -q set Master 7%-")

  -- Increase volume.
  , ((0, 0x1008FF13),
     spawn "amixer -q set Master 7%+")

  -- Audio previous.
  , ((0, 0x1008FF16),
     spawn "ncmpcpp prev")

  -- Play/pause.
  , ((0, 0x1008FF14),
     spawn "~/.xmonad/bin/mpd-stop-start")

  -- Audio next.
  , ((0, 0x1008FF17),
     spawn "ncmpcpp next")

  -- Printscreen
  , ((0, 0xff61),
     spawn "~/.xmonad/bin/screenshot")

  -- Eject CD tray.
  , ((mod1Mask, xK_e),
     spawn "eject -T")

  --------------------------------------------------------------------
  -- "Standard" xmonad key bindings
  --

  -- Close focused window.
  , ((modMask .|. shiftMask, xK_c),
     kill)

  -- Cycle through the available layout algorithms.
  , ((modMask, xK_space),
     sendMessage NextLayout)

  --  Reset the layouts on the current workspace to default.
  , ((modMask .|. shiftMask, xK_space),
     setLayout $ XMonad.layoutHook conf)

  -- Resize viewed windows to the correct size.
  , ((modMask, xK_n),
     refresh)

  -- Move focus to the next window.
  , ((modMask, xK_Tab),
     windows W.focusDown)

  -- Move focus to the next window.
  , ((modMask, xK_j),
     windows W.focusDown)

  -- Move focus to the previous window.
  , ((modMask, xK_k),
     windows W.focusUp  )

  -- Move focus to the master window.
  , ((modMask, xK_m),
     windows W.focusMaster  )

  -- Swap the focused window and the master window.
  , ((modMask, xK_Return),
     windows W.swapMaster)

  -- Swap the focused window with the next window.
  , ((modMask .|. shiftMask, xK_j),
     windows W.swapDown  )

  -- Swap the focused window with the previous window.
  , ((modMask .|. shiftMask, xK_k),
     windows W.swapUp    )

  -- Shrink the master area.
  , ((modMask, xK_h),
     sendMessage Shrink)

  -- Expand the master area.
  , ((modMask, xK_l),
     sendMessage Expand)

  -- Push window back into tiling.
  , ((modMask, xK_t),
     withFocused $ windows . W.sink)

  -- Increment the number of windows in the master area.
  , ((modMask, xK_comma),
     sendMessage (IncMasterN 1))

  -- Decrement the number of windows in the master area.
  , ((modMask, xK_period),
     sendMessage (IncMasterN (-1)))

  -- Quit xmonad.
  , ((modMask .|. shiftMask, xK_q),
     io (exitWith ExitSuccess))

  -- Restart xmonad.
  , ((modMask, xK_q),
     restart "xmonad" True)
  ]
  ++

  -- mod-[1..9], Switch to workspace N
  -- mod-shift-[1..9], Move client to workspace N
  [((m .|. modMask, k), windows $ f i)
      | (i, k) <- zip (XMonad.workspaces conf) [xK_1 .. xK_9]
      , (f, m) <- [(W.greedyView, 0), (W.shift, shiftMask)]]
--  ++

  -- mod-{w,e,r}, Switch to physical/Xinerama screens 1, 2, or 3
  -- mod-shift-{w,e,r}, Move client to screen 1, 2, or 3
--  [((m .|. modMask, key), screenWorkspace sc >>= flip whenJust (windows . f))
--      | (key, sc) <- zip [xK_w, xK_e, xK_r] [0..]
--      , (f, m) <- [(W.view, 0), (W.shift, shiftMask)]]


------------------------------------------------------------------------
-- Mouse bindings
--
-- Focus rules
-- True if your focus should follow your mouse cursor.
myFocusFollowsMouse :: Bool
myFocusFollowsMouse = True

myMouseBindings (XConfig {XMonad.modMask = modMask}) = M.fromList $
  [
    -- mod-button1, Set the window to floating mode and move by dragging
    ((modMask, button1),
     (\w -> focus w >> mouseMoveWindow w))

    -- mod-button2, Raise the window to the top of the stack
    , ((modMask, button2),
       (\w -> focus w >> windows W.swapMaster))

    -- mod-button3, Set the window to floating mode and resize by dragging
    , ((modMask, button3),
       (\w -> focus w >> mouseResizeWindow w))

    -- you may also bind events to the mouse scroll wheel (button4 and button5)
  ]


------------------------------------------------------------------------
-- Status bars and logging
-- Perform an arbitrary action on each internal state change or X event.
-- See the 'DynamicLog' extension for examples.
--
-- To emulate dwm's status bar
--
--  logHook = dynamicLogDzen
--


------------------------------------------------------------------------
-- Startup hook
-- Perform an arbitrary action each time xmonad starts or is restarted
-- with mod-q.  Used by, e.g., XMonad.Layout.PerWorkspace to initialize
-- per-workspace layout choices.
--
-- By default, do nothing.
myStartupHook = return ()


------------------------------------------------------------------------
-- Run xmonad with all the defaults we set up.
--
main = do
  xmproc <- spawnPipe "xmobar ~/.xmonad/xmobar.hs"
  xmonad $ defaults {
        manageHook = manageDocks <+> myManageHook
      , startupHook = setWMName "xmonad"
  }
									

------------------------------------------------------------------------
-- Combine it all together
-- A structure containing your configuration settings, overriding
-- fields in the default config. Any you don't override, will
-- use the defaults defined in xmonad/XMonad/Config.hs
--
-- No need to modify this.
--
defaults = defaultConfig {
    -- simple stuff
    terminal           = myTerminal,
    focusFollowsMouse  = myFocusFollowsMouse,
    borderWidth        = myBorderWidth,
    modMask            = myModMask,
    workspaces         = myWorkspaces,
    normalBorderColor  = myNormalBorderColor,
    focusedBorderColor = myFocusedBorderColor,

    -- key bindings
    keys               = myKeys,
    mouseBindings      = myMouseBindings,

    -- hooks, layouts
    layoutHook         = myLayout,
    manageHook         = myManageHook,
    startupHook        = myStartupHook
}
