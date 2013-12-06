

function processData(allText) {
	// Takes in unformatted csv as string. Parses into vertex data for a building.
    var allTextLines = allText.split(/\r\n|\n/);
    var headers = allTextLines[0].split(',');
    var data = [];
    var tCell = new Object;
    for (var i=1; i<allTextLines.length; i++){
    	//console.log(allTextLines[i]);
    	var line = allTextLines[i].split(',');
	if (i == 1){
		tCell.cell = line[2];
		tCell.floor = line[1];
		tCell.access = [];
		tCell.verts = [];
	//	tCell.entry = [];
	}
    	if (tCell.cell != line[2]){
    		data.push(tCell);
    		// If a new cell needs to be made
    		var tCell = new Object;
    		tCell.floor = line[1];
    		tCell.cell = line[2];
    		tCell.access = [];
    		tCell.verts = [];
    	//	tCell.entry = [];
    	}
    	tCell.access.push(line[6]);
    	var tVert = new Object;
    	tVert.id = line[0];
    	tVert.vType = line[3];
    	tVert.xPos = line[4];
    	tVert.yPos = line[5];
    	tVert.access = line[6];
//    	tVert.entry = line[6];
    	tVert.prev = 0;
    	tVert.dist = 999999999999999999;
    	tCell.verts.push(tVert);
    }
	//Debugging
//    for (var i=0; i < data.length; i++){
//    	console.log(data[i]);
//    }
    return data;
}

function distance(v1, v2, c){
	// for right now, return straight line distanve between verts. Future iterations will handle complex cell shapes
	return Math.sqrt(Math.pow(v1.xPos - v2.xPos,2)+Math.pow(v1.yPos-v2.yPos,2));

}

function yieldRoute(endVert){
	// Returns an array of vertices that are in the route
	var route = [endVert];
	var nextVert = endVert.prev;
	while (nextVert != 0){

		route.push(nextVert);
		nextVert = nextVert.prev;
	}
	return route;
}

function pathDistance(endVert){
	// Calculates the distance to comlpete a given path
	var nextVert = endVert.prev;
	var dist = endVert.dist;
	while (nextVert != 0){
		dist+=nextVert.dist;
		nextVert = nextVert.prev;
	}
	return dist;
}

function resetMap(buildingData){
	// Resets all the distance and previous vertices in the map 
	for (var c=0; c < buildingData.length; c++){
		for (var v=0; v < buildingData[c].verts.length; v++){
			buildingData[c].verts[v].prev = 0;
			buildingData[c].verts[v].dist = 99999999999999999;
		}
	}
}

function createQ(buildingData){
	// Returns an queue of access points in the map
	var q = [];
	for (var c=0; c < buildingData.length; c++){
		for (var v=0; v < buildingData[c].verts.length; v++){
			if (buildingData[c].verts[v].access != buildingData[c].cell){
				q.push(buildingData[c].verts[v]);
			}
		}
	}
	return q;
}

function getCell(vert, buildingData){
	// Returns the cell that a vetex is in
	for (var c=0; c < buildingData.length; c++){
		for (var v=0; v < buildingData[c].verts.length; v++){
			//console.log(vert);
			//console.log(buildingData[c].verts);
			if (vert.xPos == buildingData[c].verts[v].xPos && vert.yPos == buildingData[c].verts[v].yPos){
				// if we've found the vert.
				return buildingData[c];
			}
		}
	}
}
	
function planRoute(startPoint, destination, buildingData){
	// Main routing function. Returns an array that represents the route
	// Startpoint is always an entry point. Destination is a cell
	var start = [];
	var ends = [];
	startPoint = startPoint.toString();
	destination = destination.toString();

	// Determine the correct starting point
	if (startPoint < 0){
		// if We're entering from the outside
		for (var i = 0; i < buildingData.length; i++){
			if (buildingData[i].access.indexOf(startPoint) > -1){
				start.push([buildingData[i]]);
				for (var j=0; j < buildingData[i].verts.length; j++){
					if (buildingData[i].verts[j].entry == startPoint){
						start[start.length-1].push(buildingData[i].verts[j]);
					}
				}
			}
		}
	}
	//have starting point as cell or array of cells. List all valid end points (entrances to the desired room)
	for (var i=0; i <buildingData.length; i++){
		if (buildingData[i].access.indexOf(destination) > -1){
			ends.push([buildingData[i]]);
			for (var j=0; j < buildingData[i].verts.length; j++){
				if (buildingData[i].verts[j].access == destination){
					ends[ends.length-1].push(buildingData[i].verts[j]);
				}
			}
		}
	}

	// Begin routing. Have a list of possible start and dest verts stored as [[c1,v1,v2,v3],[c2,v1,v2,v3],...]
	// Gget a start point and just find the closest end. 
	var currentTotal = 999999999999999;
	var minE = 1;
	var meBlock = [];
	for (var eb=0; eb < ends.length; eb++){
		for (e = 1; e < ends[eb].length; e++){
			resetMap(buildingData);
			for (var i=0; i < buildingData.length; i++){
				if (buildingData[i].cell == start[0][0].cell){
					for (var j=0; j < buildingData[i].verts.length; j++){
						if (buildingData[i].verts[j].id == start[0][1].id){
							buildingData[i].verts[j].dist = 0;
						}
					}
				}
			}
			var DJQ = createQ(buildingData);
			DJQ.sort(function(a,b){return a.dist-b.dist});
			while (DJQ.length != 0){
				var q = DJQ[0];
				var c = getCell(q,buildingData);
				if (q.xPos == ends[eb][e].xPos && q.yPos == ends[eb][e].yPos){
					break;
				}
				// have cell, so all verts in cell are neighbors.
				for (var v=0; v<c.verts.length; v++){
					//var ind = DJQ.indexOf(c.verts[v]);
					//console.log(q);
					//console.log(c.verts[v]);
					var tempDist = q.dist + distance(q,c.verts[v],c);
					if (tempDist < c.verts[v].dist){
						c.verts[v].dist = tempDist;
						c.verts[v].prev = q;
					}
				}
				//REMOVE Q 
				DJQ.splice(0,1);
				DJQ.sort(function(a,b){return a.dist-b.dist});
			}
			// can find distance
			var tempTotal = pathDistance(ends[eb][e]);
			if (tempTotal < currentTotal){
				currentTotal = tempTotal;
				minE = e;
				meBlock = ends[eb];
			}
		}
	}
//	console.log("closest entrance found:");
//	console.log(meBlock[minE]);
	//know closest enterance, return the route to that entrance
	resetMap(buildingData);
	for (var i=0; i < buildingData.length; i++){
        	if (buildingData[i].cell == start[0][0].cell){
                	for (var j=0; j < buildingData[i].verts.length; j++){
                        	if (buildingData[i].verts[j].id == start[0][1].id){
                                        buildingData[i].verts[j].dist = 0;
                                }
                        }
                }
        }
	var DJQ = createQ(buildingData);
	DJQ.sort(function(a,b){return a.dist-b.dist});
	while (DJQ.length != 0){
		var q = DJQ[0];
		var c = getCell(q,buildingData);
		if (q.xPos == meBlock[minE].xPos && q.yPos == meBlock[minE].yPos){
			break;
		}
		// have cell, so all verts in cell are neighbors.
		for (var v=0; v < c.verts.length; v++){
			//var ind = DJQ.indexOf(c.verts[v]);
			var tempDist = q.dist + distance(q,c.verts[v],c);
			if (tempDist < c.verts[v].dist){
				c.verts[v].dist = tempDist;
				c.verts[v].prev = q;
			}
		}
		DJQ.splice(0,1);
		DJQ.sort(function(a,b){return a.dist-b.dist}); 
	}

	return yieldRoute(meBlock[minE]);


}

function drawRoute(PlanNode,floorPlans){
	// Used to draw the found route on the canvas
	var canvas = document.getElementById('indoorDraw');
	var context = canvas.getContext('2d');
	var startFloorImg = new Image();
	startFloorImg.src = 'images/AmosEaton2.jpg';
	startFloorImg.onload = function(){
		context.drawImage(startFloorImg,0,0,scrWidth(),scrHeight());

		//set up drawing
		var prevNode = PlanNode[0];
		var nextNode = prevNode.prev;
		while (nextNode != 0){
			context.beginPath();
			context.moveTo(prevNode.xPos, prevNode.yPos);
			context.lineTo(nextNode.xPos, nextNode.yPos);
			context.stroke();
			prevNode = nextNode;
			nextNode = prevNode.prev;
		}
	};
}
