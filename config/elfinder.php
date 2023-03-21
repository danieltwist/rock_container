<?php
if(isset($_COOKIE["role"])){
    if($_COOKIE["role"] == '3d4e992d8d8a7d848724aa26ed7f417697d495c42bda72e0ccf02d4c4294161d52380db7e19ed2885174d868a763fe93'){
        return array(

            /*
            |--------------------------------------------------------------------------
            | Upload dir
            |--------------------------------------------------------------------------
            |
            | The dir where to store the images (relative from public)
            |
            */
            'dir' => [

            ],

            /*
            |--------------------------------------------------------------------------
            | Filesystem disks (Flysytem)
            |--------------------------------------------------------------------------
            |
            | Define an array of Filesystem disks, which use Flysystem.
            | You can set extra options, example:
            |
            | 'my-disk' => [
            |        'URL' => url('to/disk'),
            |        'alias' => 'Local storage',
            |    ]
            */

            'disks' => [

            ],

            /*
            |--------------------------------------------------------------------------
            | Routes group config
            |--------------------------------------------------------------------------
            |
            | The default group settings for the elFinder routes.
            |
            */

            'route' => [
                'prefix' => 'filemanager',
                'middleware' => array('web', 'auth'), //Set to null to disable middleware filter
            ],

            /*
            |--------------------------------------------------------------------------
            | Access filter
            |--------------------------------------------------------------------------
            |
            | Filter callback to check the files
            |
            */

            'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

            /*
            |--------------------------------------------------------------------------
            | Roots
            |--------------------------------------------------------------------------
            |
            | By default, the roots file is LocalFileSystem, with the above public dir.
            | If you want custom options, you can set your own roots below.
            |
            */

            'roots' => array(
                array(
                    'driver' => 'LocalFileSystem',
                    'path' => base_path().'/storage/app/public',
                    'accessControl' => 'access',
                    'attributes' => array(
                        array(
                            'pattern' => '!^/templates!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/avatars!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/.tmb!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/.quarantine!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/.gitignore!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/excel_containers!',
                            'hidden' => true
                        )
                    )
                ),
            ),

            /*
            |--------------------------------------------------------------------------
            | Options
            |--------------------------------------------------------------------------
            |
            | These options are merged, together with 'roots' and passed to the Connector.
            | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1
            |
            */

            'options' => array(
            ),

            /*
            |--------------------------------------------------------------------------
            | Root Options
            |--------------------------------------------------------------------------
            |
            | These options are merged, together with every root by default.
            | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#root-options
            |
            */
        );
    }
    else {
        return array(

            /*
            |--------------------------------------------------------------------------
            | Upload dir
            |--------------------------------------------------------------------------
            |
            | The dir where to store the images (relative from public)
            |
            */
            'dir' => [

            ],

            /*
            |--------------------------------------------------------------------------
            | Filesystem disks (Flysytem)
            |--------------------------------------------------------------------------
            |
            | Define an array of Filesystem disks, which use Flysystem.
            | You can set extra options, example:
            |
            | 'my-disk' => [
            |        'URL' => url('to/disk'),
            |        'alias' => 'Local storage',
            |    ]
            */

            'disks' => [

            ],

            /*
            |--------------------------------------------------------------------------
            | Routes group config
            |--------------------------------------------------------------------------
            |
            | The default group settings for the elFinder routes.
            |
            */

            'route' => [
                'prefix' => 'filemanager',
                'middleware' => array('web', 'auth'), //Set to null to disable middleware filter
            ],

            /*
            |--------------------------------------------------------------------------
            | Access filter
            |--------------------------------------------------------------------------
            |
            | Filter callback to check the files
            |
            */

            'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

            /*
            |--------------------------------------------------------------------------
            | Roots
            |--------------------------------------------------------------------------
            |
            | By default, the roots file is LocalFileSystem, with the above public dir.
            | If you want custom options, you can set your own roots below.
            |
            */

            'roots' => array(
                array(
                    'driver' => 'LocalFileSystem',
                    'path' => base_path().'/storage/app/public',
                    'accessControl' => 'access',
                    'attributes' => array(
                        array(
                            'pattern' => '!^/templates!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/avatars!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/.tmb!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/.quarantine!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/.gitignore!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/excel_containers!',
                            'hidden' => true
                        ),
                        array(
                            'pattern' => '!^/Проекты!',
                            'locked' => true
                        )
                    )
                ),
            ),

            /*
            |--------------------------------------------------------------------------
            | Options
            |--------------------------------------------------------------------------
            |
            | These options are merged, together with 'roots' and passed to the Connector.
            | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1
            |
            */

            'options' => array(
            ),

            /*
            |--------------------------------------------------------------------------
            | Root Options
            |--------------------------------------------------------------------------
            |
            | These options are merged, together with every root by default.
            | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#root-options
            |
            */
            'root_options' => array(
                'defaults'   => array('read' => true, 'write' => true, 'locked'=>true),
                'disabled' => array('cut', 'copy'),
            ),
        );
    }

}

else {
    return array(

        /*
        |--------------------------------------------------------------------------
        | Upload dir
        |--------------------------------------------------------------------------
        |
        | The dir where to store the images (relative from public)
        |
        */
        'dir' => [

        ],

        /*
        |--------------------------------------------------------------------------
        | Filesystem disks (Flysytem)
        |--------------------------------------------------------------------------
        |
        | Define an array of Filesystem disks, which use Flysystem.
        | You can set extra options, example:
        |
        | 'my-disk' => [
        |        'URL' => url('to/disk'),
        |        'alias' => 'Local storage',
        |    ]
        */

        'disks' => [

        ],

        /*
        |--------------------------------------------------------------------------
        | Routes group config
        |--------------------------------------------------------------------------
        |
        | The default group settings for the elFinder routes.
        |
        */

        'route' => [
            'prefix' => 'filemanager',
            'middleware' => array('web', 'auth'), //Set to null to disable middleware filter
        ],

        /*
        |--------------------------------------------------------------------------
        | Access filter
        |--------------------------------------------------------------------------
        |
        | Filter callback to check the files
        |
        */

        'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

        /*
        |--------------------------------------------------------------------------
        | Roots
        |--------------------------------------------------------------------------
        |
        | By default, the roots file is LocalFileSystem, with the above public dir.
        | If you want custom options, you can set your own roots below.
        |
        */

        'roots' => array(
            array(
                'driver' => 'LocalFileSystem',
                'path' => base_path().'/storage/app/public',
                'accessControl' => 'access',
                'attributes' => array(
                    array(
                        'pattern' => '!^/templates!',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '!^/avatars!',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '!^/.tmb!',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '!^/.quarantine!',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '!^/.gitignore!',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '!^/excel_containers!',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '!^/Проекты!',
                        'locked' => true
                    )
                )
            ),
        ),

        /*
        |--------------------------------------------------------------------------
        | Options
        |--------------------------------------------------------------------------
        |
        | These options are merged, together with 'roots' and passed to the Connector.
        | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1
        |
        */

        'options' => array(
        ),

        /*
        |--------------------------------------------------------------------------
        | Root Options
        |--------------------------------------------------------------------------
        |
        | These options are merged, together with every root by default.
        | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#root-options
        |
        */
        'root_options' => array(
            'defaults'   => array('read' => true, 'write' => true, 'locked'=>true),
            'disabled' => array('cut', 'copy'),
        ),
    );
}

