; Script generated by the Inno Setup Script Wizard.
; SEE THE DOCUMENTATION FOR DETAILS ON CREATING INNO SETUP SCRIPT FILES!

[Setup]
; NOTE: The value of AppId uniquely identifies this application.
; Do not use the same AppId value in installers for other applications.
; (To generate a new GUID, click Tools | Generate GUID inside the IDE.)
AppId={{3EC8380A-9E6D-4BD0-BF00-8B1906FC454C}
AppName=Budibox
AppVersion=0.1
;AppVerName=Budibox 0.1
AppPublisher=Budibox
AppPublisherURL=http://www.budibox.com/
AppSupportURL=http://www.budibox.com/
AppUpdatesURL=http://www.budibox.com/
DefaultDirName={pf}\Budibox
DefaultGroupName=Budibox
OutputBaseFilename=setup
SetupIconFile=C:\wamp\www\budibox\client\dist\qml\budibox.ico
Compression=lzma
SolidCompression=yes

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"

[Tasks]
Name: "desktopicon"; Description: "{cm:CreateDesktopIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked
Name: "quicklaunchicon"; Description: "{cm:CreateQuickLaunchIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked; OnlyBelowVersion: 0,6.1

[Files]
Source: "C:\wamp\www\budibox\client\dist\budibox.exe"; DestDir: "{app}"; Flags: ignoreversion
Source: "C:\wamp\www\budibox\client\dist\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs
; NOTE: Don't use "Flags: ignoreversion" on any shared system files

[Icons]
Name: "{group}\Budibox"; Filename: "{app}\budibox.exe"
Name: "{group}\{cm:UninstallProgram,Budibox}"; Filename: "{uninstallexe}"
Name: "{commondesktop}\Budibox"; Filename: "{app}\budibox.exe"; Tasks: desktopicon
Name: "{userappdata}\Microsoft\Internet Explorer\Quick Launch\Budibox"; Filename: "{app}\budibox.exe"; Tasks: quicklaunchicon

[Run]
Filename: "{app}\budibox.exe"; Description: "{cm:LaunchProgram,Budibox}"; Flags: nowait postinstall skipifsilent
