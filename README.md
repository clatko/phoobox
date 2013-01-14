phoobox
=======

Phoobox (PHP-OO Fusebox)

An MVC approach to the traditional Fusebox framework. This is based off of the Coldfusion 3.x variety with an emphasis on separating logic into classes. "act_" files still exist, but are mostly for constructing and manipulating objects from a class library.

The "fbx_settings" files take care of singleton object instantiation which can occur at the framework level or the circuit level. This first pass contains a couple example classes:

* class_auth - for the creation, and retrieval of authentication information.

* class_db - for access to a MySQL database using mysqli or mysqlnd.

* class_mail - simple email class with protection against header injection.

* class_session - storage and retrieval of session information.

The sample application provided shows how one would implement a simple username/password-based sign-in.

Other things to note:

* the obfuscation libraries are not included yet. There is both a tidy/minifier for javascript and css that operates at run-time.

* fbx_mock and _dev/ are for developing bits and pieces for the framework before actually plugging them into the framework (this is a testing ground).

* class_* is a naming convention that allows the framework to dynamically load class libraries.

* a template engine has not been included for the layout files.

* there a bunch of missing libraries for form validation which are coming real soon.
