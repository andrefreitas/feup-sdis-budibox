import QtQuick 1.1



Rectangle {
    width: 360
    height: 360

    Image {
        id: image1
        x: 0
        y: 0
        width: 360
        height: 360
        source: "ui.png"

        Rectangle {
            id: background_email
            x: 49
            y: 231
            width: 253
            height: 33
            color: "#ffffff"
            radius: 0
            border.width: 1
            border.color: "#bfb8b8"
        }

        Rectangle {
            id: background_password
            x: 49
            y: 270
            width: 253
            height: 33
            color: "#ffffff"
            radius: 0
            border.width: 1
            border.color: "#bfb8b8"
        }

        TextInput {
            id: email
            x: 85
            y: 240
            width: 211
            height: 33
            horizontalAlignment: TextInput.AlignLeft
            opacity: 1
            smooth: true
            font.family: "Verdana"
            echoMode: TextInput.Normal
            font.pixelSize: 12
        }

        TextInput {
            id: password
            x: 85
            y: 280
            width: 211
            height: 33
            cursorVisible: false
            font.family: "Verdana"
            horizontalAlignment: TextInput.AlignLeft
            echoMode: TextInput.Password
            font.pixelSize: 12
        }

        Rectangle {
            id: login
            x: 49
            y: 309
            width: 253
            height: 33
            color: "#d46f42"
            radius: 0
            border.width: 1
            border.color: "#cb4828"

            Text {
                id: submit
                x: 0
                y: 0
                width: 253
                height: 33
                text: "LOGIN"
                wrapMode: Text.NoWrap
                font.family: "Arial Rounded MT Bold"
                font.bold: false
                verticalAlignment: Text.AlignVCenter
                horizontalAlignment: Text.AlignHCenter
                color: "#ffffff"
                font.pixelSize:16
            }


            signal buttonClick()
            onButtonClick: {
                testModel.printText(email.text.toString(), password.text.toString())
            }

            MouseArea{
            	objectName:"login_button"
                id: mousearea
                x: 0
                y: 0
                width: 253
                height: 33
                anchors.fill: parent
            }

            Component.onCompleted: {
                mousearea.clicked.connect(buttonClick)
            }

        }

        Image {
            id: image2
            x: 56
            y: 276
            width: 24
            height: 24
            source: "password.png"
        }

        Image {
            id: image3
            x: 57
            y: 236
            width: 24
            height: 24
            source: "user.png"
        }
    }
}
