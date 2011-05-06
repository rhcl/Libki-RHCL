#NoTrayIcon
#SingleInstance force

;Disable the Windows Start Button
gosub DisableStartButton

;Disable Ctrl-Alt-Delete
gosub DisableCtrlAltDel

;Disable Alt-Tab
!TAB::

;Disable Windows Key
LWin::
RWin::
#::

;Disable Win-L for locking computer
LWin::LCtrl

;Disable Ctrl-Escape to bring up start menu
^Escape::

;Disable Shift-F10
+F10::

;Disablt Ctrl-Shift-Esc
^+Escape::

;Disable Apps key
AppsKey::

;Disable Alt-F4
!F4::return

; Uncomment to allow Ctrl-Shift-Alt-l to disable keylock
;^+!l::
;gosub Unlock
;return

DisableCtrlAltDel:
Regwrite, REG_SZ, HKEY_LOCAL_MACHINE,SOFTWARE\Microsoft\Windows NT\CurrentVersion\Image File Execution Options\taskmgr.exe, Debugger, Hotkey Disabled
return

EnableCtrlAltDel:
RegDelete,HKEY_LOCAL_MACHINE,SOFTWARE\Microsoft\Windows NT\CurrentVersion\Image File Execution Options\taskmgr.exe
return

DisableStartButton:
Control, Disable, , Button1, ahk_class Shell_TrayWnd
return

EnableStartButton:
Control, Enable, , Button1, ahk_class Shell_TrayWnd
return

Unlock:
FileRead, UnlockPwd, c:\etc\libki\keylock
InputBox, UserInput, Enter Password, Enter Password, hide 
if UserInput = %UnlockPwd%
{
  MsgBox, Computer Is Now Unlocked
  gosub EnableStartButton
  gosub EnableCtrlAltDel
  ExitApp
}
else
MsgBox, Password Incorrect
return
