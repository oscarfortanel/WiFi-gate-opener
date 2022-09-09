# Simple ESP32-based gate opener
This project uses an ESP32 development board (ESP32­WROOM­32 in my case) to control a "dumb" gate opener system.  
Currently, the system can be controlled via a web application or a physical momentary button, which actuate a relay which shorts two pins in the gate opener control box.  
![Connection schematic](https://github.com/oscarfortanel/WiFi-gate-opener/blob/main/ESP32/schematic.JPG?raw=true "Connection Schematic")

## Work-in-progress
I'm not aware of any major bugs that prevent the core functionality of the project, however, there is a lot of error-handling that has not been included.  
I would also like to add some logging in the future.