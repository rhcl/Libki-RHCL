;
; AutoHotkey Version: 1.x
; Language:       English
; Platform:       Win9x/NT
; Author:         Kyle M Hall <kyle.m.hall@gmail.com>
;
; Script Function:
;	This script creates the password file for the password protected keylock scripts
;	Put the generated keylock file in C:/etc/libki/
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

InputBox, UserInput, Enter Password, Enter Password, hide 
UserHash := HashPassword( UserInput )
FileDelete, keylock
FileAppend, %UserHash%, keylock

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