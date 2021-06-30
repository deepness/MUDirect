#MUDirect:

This repository is a Proof-Of-Concept (POC) implementation of MUDirect.
MUDirect is an idea to extend MUD to also support direct communication between the IoT device and the dynamically-chosen endpoint 
(which is unknown when the manufacturer writes the MUD File).

In order to deploy MUDirect, four main components are needed:
1. MUD Manager (we used osMUD, fixed and extended it to support MUDirect).
2. Cellular app (Android/IPhone).
3. PHP/HTTP Server (we used Apache).
4. DNS Server (we used PowerDNS).

##osMUD: 
	https://github.com/osmud/osmud
	Follow the Build and Install instructions:
	1.Install OpenWrt on your Router.
	2.Create a docker Build image for osMUD code.
	3.Before building osMUD, replace the src dir contents with the files presented here:
	https://github.com/avraham-shalev/my_osMUD
	4. Build osMUD as they guide.

##Android App:
	Available at https://github.com/avraham-shalev/InternetChecker

##iOS App:
	Available at https://github.com/danibachar/IPTracker

##PHP (MUDirect) Server Code:
	inside php dir.