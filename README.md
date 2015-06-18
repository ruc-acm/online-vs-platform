Online VS Platform
=====================

This is the Online AI Versus platform that is used to kill time in summer programming vocation.

Website
--------
This part contains the website of the platform, which is mainly used to interact with the end-users and help administrators to get control of everything happening.

Judge
-------
This part is used to put two player programs(namely attacker and defender) into battle, supervise their running status and process their input/output.

There is no sand-boxing mechanism. Using a kernel security module like AppArmor or SELinux is recommended.

Note: To build the native judge library with `libboost-python`, please get a copy of boost and make a link to `/tmp/boost`, then run `bjam` in `judge` directory.