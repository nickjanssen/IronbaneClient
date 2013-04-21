


var geoToBeReturned = null;
var geoToBeMerged = null;

importScripts('http://localhost/ironbane/plugins/game/js/External/Three_r52.js');


self.addEventListener('message', function(e) {
  var data = e.data;

self.postMessage(data);
    // THREE.GeometryUtils.merge( data.geoA, data.geoB );

    // self.postMessage({geoA:data.geoA,chunk:data.pos});

  // switch (data.cmd) {
  //   case 'start':
  //     self.postMessage('WORKER STARTED: ' + data.msg);
  //     break;
  //   case 'stop':
  //     self.postMessage('WORKER STOPPED: ' + data.msg + '. (buttons will no longer work)');
  //     self.close(); // Terminates the worker.
  //     break;
  //   default:
  //     self.postMessage('Unknown command: ' + data.msg);
  // }
}, false);