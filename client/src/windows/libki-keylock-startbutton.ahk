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
^+!l::
gosub Unlock
return

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

DisableTaskBar:
WinHide ahk_class Shell_TrayWnd
return

EnableTaskBar:
WinShow ahk_class Shell_TrayWnd
return

Unlock:
FileRead, UnlockPwd, c:\etc\libki\keylock
gosub DisableTaskBar
InputBox, UserInput, Enter Password, Enter Password, hide
UserHash := HashPassword( UserInput ) 
if UserHash = %UnlockPwd%
{
  MsgBox, Computer Is Now Unlocked
  gosub EnableTaskBar
  gosub EnableStartButton
  gosub EnableCtrlAltDel
  gosub EnableTaskBar
  ExitApp
}
else
{
MsgBox, Password Incorrect
gosub EnableTaskBar
return
}

HashPassword(x)
{
   return CRC32( String2Hex( x ) )
}

CRC32(x)
{
   L := StrLen(x)>>1          ; length in bytes
   StringTrimLeft L, L, 2     ; remove leading 0x
   L = 0000000%L%
   StringRight L, L, 8        ; 8 hex digits
   x = %x%%L%                 ; standard pad
   R =  0xFFFFFFFF            ; initial register value
   Loop Parse, x
   {
      y := "0x" A_LoopField   ; one hex digit at a time
      Loop 4
      {
         R := (R << 1) ^ ( (y << (A_Index+28)) & 0x100000000)
         IfGreater R,0xFFFFFFFF
            R := R ^ 0x104C11DB7
      }
   }
   Return ~R                  ; ones complement is the CRC
}

String2Hex(x)                 ; Convert a string to hex digits
{                             ; needs SetFormat Integer, H
   Loop Parse, x
   {
      y := ASC(A_LoopField)   ; 2 digit ASCII code of chars of x, 15 < y < 256
      StringTrimLeft y, y, 2  ; Remove leading 0x
      hex = %hex%%y%
   }
   Return hex
}