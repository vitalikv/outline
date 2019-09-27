<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Webgl-editor</title>
    
  
</head>

<body>


<script src="js/three.min.js"></script>
<script src="js/jquery.js"></script>

<script src="js/dp/EffectComposer.js"></script>
<script src="js/dp/CopyShader.js"></script>
<script src="js/dp/RenderPass.js"></script>
<script src="js/dp/ShaderPass.js"></script>
<script src="js/dp/OutlinePass.js"></script>

<script src="js/dp/OrbitControls.js"></script>



<script>
var container, stats;
var camera, scene, renderer, controls;
var raycaster = new THREE.Raycaster();
var mouse = new THREE.Vector2();
var selectedObjects = [];
var composer, outlinePass;

var params = {
	edgeStrength: 3.0,
	edgeGlow: 0.0,
	edgeThickness: 1.0,
	pulsePeriod: 0,
	rotate: false,
	usePatternTexture: false
};
// Init gui

init();
animate();


function init() {
	container = document.createElement( 'div' );
	document.body.appendChild( container );
	var width = window.innerWidth;
	var height = window.innerHeight;
	renderer = new THREE.WebGLRenderer();
	
	// todo - support pixelRatio in this demo
	renderer.setSize( width, height ); 
	document.body.appendChild( renderer.domElement );
	scene = new THREE.Scene();
	scene.background = new THREE.Color( 0xffffff );
	camera = new THREE.PerspectiveCamera( 45, width / height, 0.1, 100 );
	camera.position.set( 0, 0, 8 );
	controls = new THREE.OrbitControls( camera, renderer.domElement );
	controls.minDistance = 5;
	controls.maxDistance = 20;
	controls.enablePan = false;
	controls.enableDamping = true;
	controls.dampingFactor = 0.05;
	//
	scene.add( new THREE.AmbientLight( 0xaaaaaa, 0.2 ) );
	var light = new THREE.DirectionalLight( 0xddffdd, 0.6 );
	light.position.set( 1, 1, 1 );
	light.castShadow = true;
	light.shadow.mapSize.width = 1024;
	light.shadow.mapSize.height = 1024;
	var d = 10;
	light.shadow.camera.left = - d;
	light.shadow.camera.right = d;
	light.shadow.camera.top = d;
	light.shadow.camera.bottom = - d;
	light.shadow.camera.far = 1000;
	scene.add( light );
	// model

	//
	var geometry = new THREE.BoxGeometry( 3, 3, 3 );
	for ( var i = 0; i < 20; i ++ ) {
		var material = new THREE.MeshLambertMaterial();
		material.color.setHSL( Math.random(), 1.0, 0.3 );
		var mesh = new THREE.Mesh( geometry, material );
		mesh.position.x = Math.random() * 4 - 2;
		mesh.position.y = Math.random() * 4 - 2;
		mesh.position.z = Math.random() * 4 - 2;

		mesh.scale.multiplyScalar( Math.random() * 0.3 + 0.1 );
		scene.add( mesh );
	}

	//

	// postprocessing
	composer = new THREE.EffectComposer( renderer );
	var renderPass = new THREE.RenderPass( scene, camera );
	composer.addPass( renderPass );
	outlinePass = new THREE.OutlinePass( new THREE.Vector2( window.innerWidth, window.innerHeight ), scene, camera );
	composer.addPass( outlinePass );
	
	outlinePass.visibleEdgeColor.set( '#00ff00' );
	outlinePass.hiddenEdgeColor.set( '#00ff00' );
	outlinePass.edgeStrength = Number( 10 );		// сила/прочность
	outlinePass.edgeThickness = Number( 0.1 );	// толщина


	window.addEventListener( 'resize', onWindowResize, false );
	window.addEventListener( 'mousemove', onTouchMove );
	window.addEventListener( 'touchmove', onTouchMove );
	
	function onTouchMove( event ) {
		var x, y;
		if ( event.changedTouches ) {
			x = event.changedTouches[ 0 ].pageX;
			y = event.changedTouches[ 0 ].pageY;
		} else {
			x = event.clientX;
			y = event.clientY;
		}
		mouse.x = ( x / window.innerWidth ) * 2 - 1;
		mouse.y = - ( y / window.innerHeight ) * 2 + 1;
		checkIntersection();
	}
	
	function addSelectedObject( object ) {
		selectedObjects = [];
		selectedObjects.push( object );
	}
	function checkIntersection() {
		raycaster.setFromCamera( mouse, camera );
		var intersects = raycaster.intersectObjects( [ scene ], true );
		if ( intersects.length > 0 ) {
			var selectedObject = intersects[ 0 ].object;
			addSelectedObject( selectedObject );
			outlinePass.selectedObjects = selectedObjects;
		} else {
			// outlinePass.selectedObjects = [];
		}
	}
}
function onWindowResize() {
	var width = window.innerWidth;
	var height = window.innerHeight;
	camera.aspect = width / height;
	camera.updateProjectionMatrix();
	renderer.setSize( width, height );
	composer.setSize( width, height );
}
function animate() {
	requestAnimationFrame( animate );

	controls.update();
	
	renderer.render(scene, camera);
	composer.render();
}

</script>

 

</body>

</html>