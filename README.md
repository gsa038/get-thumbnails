
Backend - REST API entrypoint for getting archive of resized (1000x1000,900x900,...,100x100) copies of uploaded image

Frontend - page with form to upload origin and download archive of copies


======================
Start environment
=====================

All commands below for debian-based systems!

Docker & docker-compose must be installed!!!

You need to install "make" utility.

Use the below command to install it:

sudo apt update && sudo apt install make

Then to start use project use:

sudo make docker-start


======================
Shutdown environment
=====================

use the below command:

sudo docker-stop

It'll stop your project and remove dependencies and temporary files
