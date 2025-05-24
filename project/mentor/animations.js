class Particle {
    constructor(canvas, options = {}) {
        this.canvas = canvas;
        this.x = options.x || Math.random() * canvas.width;
        this.y = options.y || Math.random() * canvas.height;
        this.size = Math.random() * 5 + 1;
        this.speedX = Math.random() * 3 - 1.5;
        this.speedY = Math.random() * 3 - 1.5;
        this.color = options.color || '#0ef';
    }

    update() {
        this.x += this.speedX;
        this.y += this.speedY;

        if (this.size > 0.2) this.size -= 0.1;

        // Wrap around screen
        if (this.x < 0) this.x = this.canvas.width;
        if (this.x > this.canvas.width) this.x = 0;
        if (this.y < 0) this.y = this.canvas.height;
        if (this.y > this.canvas.height) this.y = 0;
    }

    draw(ctx) {
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

class ParticleNetwork {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.mouse = {
            x: null,
            y: null,
            radius: 150
        };
        
        this.init();
        this.animate();
        this.handleResize();
        this.handleMouseMove();
    }

    init() {
        this.resizeCanvas();
        this.createParticles();
    }

    resizeCanvas() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }

    createParticles() {
        const numberOfParticles = (this.canvas.width * this.canvas.height) / 9000;
        this.particles = [];

        for (let i = 0; i < numberOfParticles; i++) {
            this.particles.push(new Particle(this.canvas));
        }
    }

    handleResize() {
        window.addEventListener('resize', () => {
            this.resizeCanvas();
            this.createParticles();
        });
    }

    handleMouseMove() {
        window.addEventListener('mousemove', (event) => {
            this.mouse.x = event.x;
            this.mouse.y = event.y;
        });
    }

    drawConnections() {
        for (let i = 0; i < this.particles.length; i++) {
            for (let j = i; j < this.particles.length; j++) {
                const dx = this.particles[i].x - this.particles[j].x;
                const dy = this.particles[i].y - this.particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 100) {
                    this.ctx.beginPath();
                    this.ctx.strokeStyle = `rgba(14, 255, 255, ${1 - distance/100})`;
                    this.ctx.lineWidth = 0.5;
                    this.ctx.moveTo(this.particles[i].x, this.particles[i].y);
                    this.ctx.lineTo(this.particles[j].x, this.particles[j].y);
                    this.ctx.stroke();
                }
            }
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.particles.forEach(particle => {
            particle.update();
            particle.draw(this.ctx);

            // Interactive effect with mouse
            if (this.mouse.x) {
                const dx = particle.x - this.mouse.x;
                const dy = particle.y - this.mouse.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < this.mouse.radius) {
                    const force = (this.mouse.radius - distance) / this.mouse.radius;
                    const directionX = dx / distance;
                    const directionY = dy / distance;
                    particle.x += directionX * force * 2;
                    particle.y += directionY * force * 2;
                }
            }
        });

        this.drawConnections();
        requestAnimationFrame(() => this.animate());
    }
}

// Initialize ripple effect
class RippleEffect {
    constructor(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.ripples = [];
        this.init();
    }

    init() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;

        window.addEventListener('click', (e) => this.createRipple(e.x, e.y));
        window.addEventListener('resize', () => {
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
        });

        this.animate();
    }

    createRipple(x, y) {
        this.ripples.push({
            x,
            y,
            radius: 0,
            alpha: 1,
            speed: 5
        });
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        for (let i = this.ripples.length - 1; i >= 0; i--) {
            const ripple = this.ripples[i];
            ripple.radius += ripple.speed;
            ripple.alpha -= 0.01;

            this.ctx.beginPath();
            this.ctx.arc(ripple.x, ripple.y, ripple.radius, 0, Math.PI * 2);
            this.ctx.strokeStyle = `rgba(14, 255, 255, ${ripple.alpha})`;
            this.ctx.stroke();

            if (ripple.alpha <= 0) {
                this.ripples.splice(i, 1);
            }
        }

        requestAnimationFrame(() => this.animate());
    }
}

class MovingLines {
    constructor(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.lines = [];
        this.colors = ['#0ef', '#ff0099', '#00ff99', '#ff9900', '#9900ff'];
        this.init();
    }

    init() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;

        // Create initial lines
        for (let i = 0; i < 50; i++) {
            this.createLine();
        }

        window.addEventListener('resize', () => {
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
            this.lines = [];
            for (let i = 0; i < 50; i++) {
                this.createLine();
            }
        });

        this.animate();
    }

    createLine() {
        const line = {
            x: Math.random() * this.canvas.width,
            y: Math.random() * this.canvas.height,
            length: Math.random() * 100 + 50,
            angle: Math.random() * Math.PI * 2,
            speed: Math.random() * 2 + 0.5,
            color: this.colors[Math.floor(Math.random() * this.colors.length)],
            thickness: Math.random() * 3 + 1,
            oscillation: {
                amplitude: Math.random() * 2,
                frequency: Math.random() * 0.02,
                offset: Math.random() * Math.PI * 2
            }
        };
        this.lines.push(line);
    }

    updateLine(line) {
        // Move the line
        line.x += Math.cos(line.angle) * line.speed;
        line.y += Math.sin(line.angle) * line.speed;

        // Add oscillating motion
        line.angle += Math.sin(Date.now() * line.oscillation.frequency + line.oscillation.offset) * line.oscillation.amplitude * 0.02;

        // Wrap around screen
        if (line.x < -line.length) line.x = this.canvas.width + line.length;
        if (line.x > this.canvas.width + line.length) line.x = -line.length;
        if (line.y < -line.length) line.y = this.canvas.height + line.length;
        if (line.y > this.canvas.height + line.length) line.y = -line.length;
    }

    drawLine(line) {
        const endX = line.x + Math.cos(line.angle) * line.length;
        const endY = line.y + Math.sin(line.angle) * line.length;

        this.ctx.beginPath();
        this.ctx.strokeStyle = line.color;
        this.ctx.lineWidth = line.thickness;
        this.ctx.lineCap = 'round';
        this.ctx.globalAlpha = 0.7;
        this.ctx.moveTo(line.x, line.y);
        this.ctx.lineTo(endX, endY);
        this.ctx.stroke();
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.lines.forEach(line => {
            this.updateLine(line);
            this.drawLine(line);
        });

        requestAnimationFrame(() => this.animate());
    }
}

// Initialize everything when the window loads
window.addEventListener('load', () => {
    // Create canvas elements
    const particleCanvas = document.createElement('canvas');
    const rippleCanvas = document.createElement('canvas');
    const linesCanvas = document.createElement('canvas');
    
    // Set canvas styles
    const canvasStyle = {
        position: 'fixed',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        pointerEvents: 'none',
        zIndex: 1
    };
    
    Object.assign(particleCanvas.style, {...canvasStyle, zIndex: 3});
    Object.assign(rippleCanvas.style, {...canvasStyle, zIndex: 2});
    Object.assign(linesCanvas.style, {...canvasStyle, zIndex: 1});
    
    // Add IDs
    particleCanvas.id = 'particleCanvas';
    rippleCanvas.id = 'rippleCanvas';
    linesCanvas.id = 'linesCanvas';
    
    // Add to document
    document.body.prepend(particleCanvas);
    document.body.prepend(rippleCanvas);
    document.body.prepend(linesCanvas);
    
    // Initialize animations
    new ParticleNetwork('particleCanvas');
    new RippleEffect(rippleCanvas);
    new MovingLines(linesCanvas);
});