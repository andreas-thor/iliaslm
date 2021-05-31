cd /D "%~dp0"
cd cp

"C:\Program Files\php\php.exe" Content_Package_Generator.php "C:\Lehre\VL_Prog\lernmodul\java.json" ..\tmp
REM "C:\Program Files\php\php.exe" Content_Package_Generator.php "C:\Lehre\VL_DBS\lernmodul\dbs.json" ..\tmp

REM für commit