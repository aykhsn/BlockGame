<?php
/*
Template Name: ドキドキびっくりばこ
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php Arkhe::root_attrs(); ?>>
<head>
<meta charset="utf-8">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, viewport-fit=cover">
<?php
	wp_head();
	$setting = Arkhe::get_setting(); // SETTING取得
?>
<style>
    html, body {
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #fdefa9;
        overflow: hidden;
    }
</style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/phaser@3/dist/phaser.min.js"></script>
    <script>
        const assetsUrl = "<?php echo get_template_directory_uri(); ?>/assets/games";

        const config = {
            type: Phaser.AUTO,
            width: window.innerWidth,
            height: window.innerHeight,
            backgroundColor: '#fdefa9',
            scale: {
                mode: Phaser.Scale.FIT,
                autoCenter: Phaser.Scale.CENTER_BOTH
            },
            scene: {
                preload: preload,
                create: create,
                update: update
            }
        };

        const game = new Phaser.Game(config);
        let boxTween;
        let shapes = [];
        let isBoxOpen = false;
        let animationPlaying = false;
        let characters = ['neko', 'usagi', 'bird', 'banana'];
        let character;

        function preload() {
            this.load.image('box', `${assetsUrl}/box.png`);
            this.load.image('box-open', `${assetsUrl}/box-open.png`);
            this.load.image('lid', `${assetsUrl}/lid.png`);
            this.load.image('neko', `${assetsUrl}/neko2_ba.png`);
            this.load.image('usagi', `${assetsUrl}/rabbit2_ba.png`);
            this.load.image('bird', `${assetsUrl}/pikopo_ba.png`);
            this.load.image('banana', `${assetsUrl}/banana_ba.png`);
            this.load.image('spring', `${assetsUrl}/spring.png`);
            this.load.audio('boyoyon', `${assetsUrl}/boyoyon.mp3`);
            this.load.audio('mokkin', `${assetsUrl}/mokkin.mp3`);
            this.load.audio('jajan', `${assetsUrl}/jajan.mp3`);
            this.load.audio('panpakapan', `${assetsUrl}/panpakapan.mp3`);
            this.load.audio('yahho', `${assetsUrl}/yahho.mp3`);
            this.load.audio('yatta', `${assetsUrl}/yatta.mp3`);
            this.load.audio('sonochoshi', `${assetsUrl}/sonochoshi.mp3`);
            this.load.audio('banzai', `${assetsUrl}/banzai.mp3`);
            this.load.audio('haihai', `${assetsUrl}/haihai.mp3`);
            this.load.audio('tararara', `${assetsUrl}/tararara.mp3`);
            this.load.audio('dododo', `${assetsUrl}/dododo.mp3`);
        }

        function create() {
            const groundHeight = this.scale.height * 0.1;
            const boxHeight = Math.min(this.scale.height, this.scale.width) / 2.8;
            const boxWidth = boxHeight * (this.textures.get('box').getSourceImage().width / this.textures.get('box').getSourceImage().height);

            this.box = this.add.sprite(this.scale.width / 2, this.scale.height - boxHeight / 2 - groundHeight, 'box');
            this.box.setDisplaySize(boxWidth, boxHeight);

            this.lid = this.add.sprite(this.scale.width / 2, this.scale.height - boxHeight - (boxHeight / 5) - groundHeight, 'lid');
            this.lid.setDisplaySize(boxWidth * 1.08, boxHeight * 53 / 80);
            this.lid.setAlpha(1);

            const springHeight = boxHeight / 2;
            this.spring = this.add.sprite(this.scale.width / 2, this.scale.height - boxHeight - groundHeight, 'spring');
            this.spring.setOrigin(0.5, 0.9);
            this.spring.setDisplaySize(boxWidth * 0.4, springHeight);
            this.spring.setDepth(-2);
            this.spring.setAlpha(0);

            this.neko = this.add.sprite(this.scale.width / 2, this.scale.height - boxHeight - groundHeight - springHeight / 2, 'neko');
            this.neko.setDisplaySize(boxWidth * 1.2, boxHeight * 1.8);
            this.neko.setDepth(-1);
            this.neko.setAlpha(0);

            this.usagi = this.add.sprite(this.scale.width / 2, this.scale.height - boxHeight - groundHeight - springHeight / 2, 'usagi');
            this.usagi.setDisplaySize(boxWidth * 1.2, boxHeight * 1.8);
            this.usagi.setDepth(-1);
            this.usagi.setAlpha(0);

            this.bird = this.add.sprite(this.scale.width / 2, this.scale.height - boxHeight - groundHeight - springHeight / 2, 'bird');
            this.bird.setDisplaySize(boxWidth * 1.2, boxHeight * 1.8);
            this.bird.setDepth(-1);
            this.bird.setAlpha(0);

            this.banana = this.add.sprite(this.scale.width / 2, this.scale.height - boxHeight - groundHeight - springHeight / 2, 'banana');
            this.banana.setDisplaySize(boxWidth * 1.2, boxHeight * 1.8);
            this.banana.setDepth(-1);
            this.banana.setAlpha(0);

            this.sound.add('boyoyon');
            this.sound.add('mokkin');
            this.sound.add('jajan');
            this.sound.add('panpakapan');
            this.sound.add('yahho');
            this.sound.add('yatta');
            this.sound.add('sonochoshi');
            this.sound.add('banzai');
            this.sound.add('haihai');
            this.sound.add('tararara');
            this.sound.add('dododo');

            this.box.setInteractive();
            this.box.on('pointerdown', handleBoxClick, this);

            const resetBox = () => {
                this.sound.play('tararara');
                this.box.setTexture('box');
                this.box.setInteractive();
                this.box.y = this.scale.height - boxHeight / 2 - groundHeight;
                this.lid.x = this.scale.width / 2;
                this.lid.y = this.scale.height - boxHeight - (boxHeight / 5) - groundHeight;
                this.lid.angle = 0;
                this.lid.alpha = 1;
                this.spring.setAlpha(0);
                this.spring.setDisplaySize(boxWidth * 0.4, springHeight); // reset spring height
                this.neko.setAlpha(0);
                this.neko.y = this.scale.height - boxHeight - groundHeight - springHeight / 2;
                this.neko.disableInteractive();
                this.usagi.setAlpha(0);
                this.usagi.y = this.scale.height - boxHeight - groundHeight - springHeight / 2;
                this.usagi.disableInteractive();
                this.bird.setAlpha(0);
                this.bird.y = this.scale.height - boxHeight - groundHeight - springHeight / 2;
                this.bird.disableInteractive();
                this.banana.setAlpha(0);
                this.banana.y = this.scale.height - boxHeight - groundHeight - springHeight / 2;
                this.banana.disableInteractive();
                shapes.forEach(shape => shape.destroy());
                shapes = [];
                isBoxOpen = false;
                animationPlaying = false; // アニメーションが終わったことを示す
            };

            function createShapes(x, y, size) {
                const colors = [0xf0abab, 0xabf3ab, 0xafaff4, 0xffd27e, 0xf1abf1, 0x91d7d8];
                const shapes = [];
                for (let i = 0; i < 20; i++) {
                    const color = Phaser.Display.Color.IntegerToColor(colors[Math.floor(Math.random() * colors.length)]);
                    let shape;
                    if (i % 3 === 0) {
                        shape = this.add.star(x, y, 5, size * 0.1, size * 0.2, color.color);
                    } else if (i % 3 === 1) {
                        shape = this.add.circle(x, y, size * 0.1, color.color);
                    } else {
                        shape = this.add.triangle(x, y, 0, 0, size * 0.2, 0, size * 0.1, size * 0.2, color.color);
                    }
                    shape.setStrokeStyle(3, 0x000000);
                    shape.setDepth(-3);
                    shapes.push(shape);
                }
                return shapes;
            }

            function handleBoxClick() {
                if (animationPlaying) return; // アニメーション中はクリックを無効化
                if (isBoxOpen) {
                    resetBox();
                } else {
                    const dododoSound = this.sound.add('dododo');
                    dododoSound.play();
                    this.time.delayedCall(2000, () => {
                        dododoSound.stop();
                    });

                    this.box.disableInteractive();
                    animationPlaying = true; // アニメーション開始

                    boxTween = this.tweens.add({
                        targets: [this.box, this.lid],
                        y: '-=10',
                        yoyo: true,
                        repeat: 6,
                        duration: 140,
                        ease: 'Sine.easeInOut',
                        onComplete: () => {
                            this.sound.play('boyoyon');
                            this.sound.play('mokkin');
                            this.sound.play('jajan');

                            const sounds = ['panpakapan', 'yahho', 'yatta', 'sonochoshi', 'banzai', 'haihai'];
                            const randomSound = Phaser.Utils.Array.GetRandom(sounds);
                            this.time.delayedCall(600, () => {
                                this.sound.play(randomSound);
                            });

                            this.box.setTexture('box-open');
                            isBoxOpen = true;

                            character = Phaser.Utils.Array.GetRandom(characters);

                            this.lid.setAlpha(1); // 蓋を表示

                            this.tweens.add({
                                targets: this.lid,
                                x: this.lid.x + 400,
                                y: this.lid.y - 400,
                                angle: 120,
                                alpha: 0,
                                duration: 1000,
                                ease: 'Power2',
                                onComplete: () => {
                                    this.lid.x = this.scale.width / 2;
                                    this.lid.y = this.scale.height - boxHeight - (boxHeight / 5) - groundHeight;
                                    this.lid.angle = 0;
                                    this.lid.alpha = 0; // 完了時に透明に設定
                                }
                            });

                            this.tweens.add({
                                targets: this.spring,
                                alpha: 1,
                                displayHeight: (this.scale.height - boxHeight * 1.8 - groundHeight) / 2,
                                duration: 500,
                                ease: 'Bounce.easeOut',
                                onUpdate: () => {
                                    this[character].y = this.spring.y - this.spring.displayHeight + springHeight / 2;
                                }
                            });

                            this.tweens.add({
                                targets: this[character],
                                y: (this.scale.height - boxHeight - groundHeight) / 2,
                                alpha: 1,
                                duration: 500,
                                ease: 'Bounce.easeOut',
                            });

                            shapes = createShapes.call(this, this.box.x, this.box.y, boxWidth);
                            shapes.forEach(shape => {
                                this.tweens.add({
                                    targets: shape,
                                    x: shape.x + (Math.random() * 400 - 200),
                                    y: shape.y - (Math.random() * 400 + 100),
                                    duration: 1500,
                                    ease: 'Cubic.easeOut'
                                });
                            });

                            this[character].setInteractive();
                            this[character].on('pointerdown', () => {
                                this.sound.play('boyoyon');

                                this.tweens.add({
                                    targets: this[character],
                                    y: this[character].y + 20,
                                    yoyo: true,
                                    repeat: 6,
                                    duration: 100,
                                    ease: 'Sine.easeInOut'
                                });

                                this.tweens.add({
                                    targets: this.spring,
                                    displayHeight: this.spring.displayHeight * 0.8,
                                    yoyo: true,
                                    repeat: 6,
                                    duration: 100,
                                    ease: 'Sine.easeInOut'
                                });
                            });

                            this.time.delayedCall(2000, () => {
                                this.box.setInteractive();
                                animationPlaying = false; // アニメーション終了
                            });
                        }
                    });
                }
            }
        }

        function update() {}

        window.addEventListener('resize', () => {
            game.scale.resize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>