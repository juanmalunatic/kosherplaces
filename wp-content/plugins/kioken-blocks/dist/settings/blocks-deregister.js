let unregisteredBlocks = kt_deregister_params.deregister;
let unregisteredPlugins = kt_deregister_params.dergisterplugins;

// If we are recieving an object
// let's convert it into an array
if ( ! unregisteredBlocks.length ) {
	unregisteredBlocks =
		Object.keys( unregisteredBlocks ).map( key => unregisteredBlocks[ key ] );
}
if ( ! unregisteredPlugins.length ) {
	unregisteredPlugins =
		Object.keys( unregisteredPlugins ).map( key => unregisteredPlugins[ key ] );
}

// Just in case let's check if function exists
if ( typeof wp.blocks.unregisterBlockType !== 'undefined' ) {
	unregisteredBlocks.forEach( block => wp.blocks.unregisterBlockType( block ) );
}
if ( typeof wp.plugins.unregisterPlugin !== 'undefined' ) {
	unregisteredPlugins.forEach( plugin => wp.plugins.unregisterPlugin( plugin ) );
}
