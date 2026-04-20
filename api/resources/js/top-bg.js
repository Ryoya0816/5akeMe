import * as THREE from "three";

console.log("top-bg: loaded");

document.addEventListener("DOMContentLoaded", () => {
  // TOPページ以外では何もしない
  if (!document.querySelector('[data-page="top"]')) {
    console.log("top-bg: skip (not top)");
    return;
  }

  const stage = document.getElementById("top-bg-stage");
  console.log("top-bg: stage =", stage);
  if (!stage) return;

  console.log("🔥 top-bg init");

  const reduceMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)"
  ).matches;
  if (reduceMotion) return;

  const renderer = new THREE.WebGLRenderer({
    antialias: true,
    alpha: true,
  });

  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.setClearColor(0x000000, 0);

  const canvas = renderer.domElement;
  canvas.style.position = "fixed";
  canvas.style.inset = "0";
  canvas.style.width = "100%";
  canvas.style.height = "100%";
  canvas.style.pointerEvents = "none";

  stage.appendChild(canvas);

  const scene = new THREE.Scene();
  const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.01, 10);
  camera.position.z = 1;

  const geometry = new THREE.PlaneGeometry(2, 2);

  const pointer = new THREE.Vector2(0, 0);
  const target = new THREE.Vector2(0, 0);

  const material = new THREE.ShaderMaterial({
    transparent: true,
    depthWrite: false,
    uniforms: {
      uPointer: { value: new THREE.Vector2(0, 0) },
      uStrength: { value: 0.16 },
      uRadius: { value: 0.55 },
    },
    vertexShader: `
      varying vec2 vUv;
      void main() {
        vUv = uv;
        gl_Position = vec4(position.xy, 0.0, 1.0);
      }
    `,
    fragmentShader: `
      varying vec2 vUv;
      uniform vec2 uPointer;
      uniform float uStrength;
      uniform float uRadius;

      vec2 toNdc(vec2 uv) {
        return uv * 2.0 - 1.0;
      }

      void main() {
        vec2 p = toNdc(vUv);
        float d = distance(p, uPointer);
        float a = smoothstep(uRadius, 0.0, d) * uStrength;
        gl_FragColor = vec4(0.0, 0.0, 0.0, a);
      }
    `,
  });

  scene.add(new THREE.Mesh(geometry, material));

  function resize() {
    renderer.setSize(window.innerWidth, window.innerHeight, false);
  }

  window.addEventListener("resize", resize);
  resize();

  window.addEventListener("pointermove", (e) => {
    const x = (e.clientX / window.innerWidth) * 2 - 1;
    const y = -(e.clientY / window.innerHeight) * 2 + 1;
    target.set(x, y);
  });

  function animate() {
    pointer.lerp(target, 0.12);
    material.uniforms.uPointer.value.copy(pointer);
    renderer.render(scene, camera);
    requestAnimationFrame(animate);
  }

  animate();
});
