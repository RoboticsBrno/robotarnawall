# robotarnawall
Simple PHP web page for quick sharing information during lecture or event.

## Available tags

### Tag [code]
To put on the wall text as source code (C/C++, Python, ...).

```
[code]
// source:  http://platformio.org/lib/show/1739/ServoESP32
#include "LearningKit.h"
#include <Servo.h>

static const int servoPin = S1;
static const int potentiometerPin = 32;

Servo servo1;

void setup() {
    Serial.begin(115200);
    servo1.attach(servoPin);
}

void loop() {
    int servoPosition = map(analogRead(potentiometerPin), 0, 4096, 0, 180);
    servo1.write(servoPosition);
    Serial.println(servoPosition);
    delay(20);
}[/code]
```

### Tag [raw]
To put on the wall HTML elements and code which you want to process on the user side (in the browser).

```
[raw]
<div style="position:relative;height:0;padding-bottom:81.97%;overflow:hidden;"><iframe style="position:absolute;top:0;left:0;width:100%;height:100%;" src="https://makecode.microbit.org/---run?id=_KvzMqyayA7cb" allowfullscreen="allowfullscreen" sandbox="allow-popups allow-forms allow-scripts allow-same-origin" frameborder="0"></iframe></div>
[/raw]
```
