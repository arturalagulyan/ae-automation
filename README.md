# Celebrity Simulator Backend

## Installation

- Clone project from repository - ``git clone git@github.com:RobertPS62/celebritysimulator.git``
- Create DB and set credentials in .env file
- In project directory run ``composer install`` then ``npm install``

## Executing project from command line

- Add project files in ``'D:\\template-projects\\'`` and assets in ``'D:\\assets\\'`` (both can be managed from config)
- Start nexrender server via ``nexrender-server --port=3050 --secret=test`` (the ``test`` key can be managed from config)
- From command line change directory to ``celebritysimulator`` destination (wherever you cloned the repository).
- Run ``php artisan render {--project=}`` command (in case of ``paparazzi-themed`` project it will be ``php artisan render --project paparazzi-themed``)

## Project structure

Is a .json file which contains information how rendering and replication must be processed. Each of this files is responsible for individual project. Initially location must be in ``'D:\\backend-projects\\'`` (can be managed from config).

```sh
{
    "replication": {
        "template": TEMPLATE_NAME,
        "options": {
            "worker": [
                ...WORKER_OPTIONS (array)
            ]
        }
    },
    "rendering": {
        "composition": COMPOSITION_NAME,
        "filename": FILE_NAME,
        "sequence_n": SEQUENCE_N,
        "options": {
            "wav": [
                ...WAV_COMMAND_OPTIONS (array)
            ],
            "ffmpeg": [
                ...FFMPEG_COMMAND_OPTIONS (array)
            ],
            "seq": [
                ...FRAMES_COMMAND_OPTIONS (array)
            ]
        }
    }
}
```

- **TEMPLATE_NAME**: The name of template located in ``'D:\\template-projects\\'`` (can be managed from config) which must be processed. Example: ``paparazzi-themed``
- **WORKER_OPTIONS**: Additional options for ``nexrender-worker`` command. Example:
```
[
    "--skip-render",
    "--skip-cleanup",
    "--aerender-parameter \"close SAVE_CHANGES\""
]
```
- **COMPOSITION_NAME**: The name of composition. Example: ``!FINAL``
- **FILE_NAME**: Output file name. Example: ``paparazzi-v1-1``
- **SEQUENCE_N**: Option defining how many times frames command must work. Example: ``7``
- **WAV_COMMAND_OPTIONS**: Additional options for WAV command. Example:
```
[
    "-OMtemplate \"wav-audio\""
]
```
- **FFMPEG_COMMAND_OPTIONS**: Additional options for FFMPEG command. Example:
```
[
    "-r 25",
    "-start_number 0000",
    "-f image2"
]
```
- **FRAMES_COMMAND_OPTIONS**: Additional options for Frames command. Example:
```
[
    "-RStemplate multi-best-full",
    "-OMtemplate jpeg-seq"
]
```

## Full example
```
{
    "replication": {
        "template": "paparazzi-themed",
        "options": {
            "worker": [
                "--skip-render",
                "--skip-cleanup",
                "--aerender-parameter \"close SAVE_CHANGES\""
            ]
        }
    },
    "rendering": {
        "composition": "!FINAL",
        "filename": "paparazzi-v1-1",
        "sequence_n": 7,
        "options": {
            "wav": [
                "-OMtemplate \"wav-audio\""
            ],
            "ffmpeg": [
                "-r 25",
                "-start_number 0000",
                "-f image2"
            ],
            "seq": [
                "-RStemplate multi-best-full",
                "-OMtemplate jpeg-seq"
            ]
        }
    }
}
```
All directory and program paths can be managed from ``/config/renderer.php`` file

## Directories

| Folder | Destination |
| ------ | ------ |
| Replication folder | ``C:\\nexrender\\`` |
| Assets folder | ``D:\\assets\\`` |
| Templates folder | ``D:\\template-projects\\`` |
| Render folder | ``D:\\renders\\`` |
| Outputs folder | ``D:\\final-outputs\\`` |
| Logs folder | ``D:\\logs\\`` |

## Programs

| Program | Destination |
| ------ | ------ |
| After Effects | ``C:\\"Program Files"\\Adobe\\"Adobe After Effects 2020"\\"Support Files"\\aerender.exe`` |
| FFMPEG | ``C:\\ffmpeg\\ffmpeg-b4.2.2.exe`` |


