;
; AutoHotkey Version: 1.x
; Language:       English
; Platform:       Win9x/NT
; Author:         Kyle Hall <kyle.m.hall@gmail.com>
;
; Script Function:
;	This script is launched by the Libki client on windows to prevent a user from
;	bypassing the client by opening another window before it runs.
;
#SingleInstance force

gosub EnableTaskBar
gosub ShowDesktopIcons
ExitApp

DisableTaskBar:
WinHide ahk_class Shell_TrayWnd
return

EnableTaskBar:
WinShow ahk_class Shell_TrayWnd
return

HideDesktopIcons:
winhide, Program Manager
return

ShowDesktopIcons:
winshow, Program Manager
