//TODO: 
//Read csv in javascript. maybe jQuery and node.js?
//More properly describe a cell and a vertex.
//Cell:
// cell id
// vert list containg all geometric points that make the cell shape
// Access point list and where they lead into
//Vert:
// which cell this vert is a part of.
// the two neighbors for this vertex. This is for the disambiguation of the final path and for calculating the angle.

// Building data: 


function processData(allText) {
    var allTextLines = allText.split(/\r\n|\n/);
    var headers = allTextLines[0].split(',');
    var lines = [];
    var tCell = new Object;
    tCell.cell = -1;
    for (var i=1; i<allTextLines.length; i++) {
        var data = allTextLines[i].split(',');
        if (tCell.cell != data[1]){
        	//change in cell
        	if (i!=1){
        		//dont push the first change
        		//delete duplicates in access array
        		tCell.access = jQuery.unique(tCell.access);
        		tCell.entry = jQuery.unique(tCell.entry);
        		lines.push(tCell);
        	}
        	var tCell = new Object;
        	tCell.floor = data[0];
        	tCell.cell = data[1];
        	tCell.access = [];
        	tCell.verts = [];
        	tCell.entry = [];
        }
        //add in verts
        tCell.access.push(data[5]);
        tCell.entry.push(data[6]);
       	var tVert = new object;
       	tVert.vType = data[2];
       	tVert.xPos = data[3];
       	tVert.yPos = data[4];
       	tVert.access = data[5];
       	tVert.entry = data[6];
       	tVert.prev = 0;
       	tVert.dist = 999999999999999;
       	tCell.verts.push(tVert);

        // if (data.length == headers.length) {

        //     var tCell = new Object;
        //     tVert.floor = data[0];
        //     tVert.cell = data[1];
        //     tVert.vType = data[2];
        //     tVert.xPos = data[3];
        //     tVert.yPos = data[4];
        //     tVert.access = data[5];
        //     console.log("Pushing new object for cell ", tVert.cell)

        //     lines.push(tVert);
        // }
    }
    return lines;
    // alert(lines);
}

function distance(v1, v2){

}

function yieldRoute(endVert){
	var route = [];
	var nextVert = endVert;
	while (nextVert != 0){

		route.push(nextVert);
		nextVert = nextVert.prev;
	}
	return route;
}

function pathDistance(endVert){
	var nextVert = endVert.prev;
	var dist = 0;
	while (nextVert != 0){
		dist+=nextVert.dist;
		nextVert = nextVert.prev;
	}
	return dist;
}

function resetMap(buildingData){
	for (cell in buildingData){
		for (vert in cell.verts){
			vert.prev = 0;
			vert.dist = 9999999999999999;
		}
	}
}

function createQ(buildingData){
	var q = [];
	for (cell in buildingData){
		for (vert in cell.verts){
			if (vert.access != cell.cell){
				q.push(vert);
			}
		}
	}
}

function getCell(v, buildingData){
	for (cell in buildingData){
		for (vert in cell.verts){
			if (v.xPos == vert.xPos && v.yPos == vert.yPos && v.access == vert.access){
				// if we've found the vert.
				return cell;
			}
		}
	}
}

function planRoute(startPoint, destination, buildingData){
	// Startpoint is always an entry point. Destination is a cell
	var start = [];
	var ends = [];
	if (startPoint < 0){
		// if We're entering from the outside
		for (int i = 0; i < buildingData.length; i++){
			if (buildingData[i].entry.indexOf(startPoint) > -1){
				start.push([buildingData[i]]);
				for (int j=0; j < buildingData[i].verts.length; j++){
					if (buildingData[i].verts[j].entry == startPoint){
						start[start.length-1].push(buildingData[i].verts[j]);
					}
				}
			}
		}
	}
	else{
		// indoor starting point. will likely be routing to exit
		for (int i=0; i < buildingData.length; i++){
			if (buildingData[i].access.indexOf(startPoint) > -1){
				start.push([buildingData[i]]);
				for (int j=0; j < buildingData[i].verts.length; j++){
					if (buildingData[i].verts[j].access == startPoint){
						start[start.length-1].push(buildingData[i].verts[j]);
					}
				}
			}
		}
	}
	//have starting point as cell or array of cells. reslove end points
	for (int i=0; i <buildingData.length; i++){
		if (buildingData[i].access.indexOf(destination) > -1){
			ends.push([buildingData[i]]);
			for (int j=0; j < buildingData[i].verts.length; j++){
				if (buildingData[i].verts[j].access == destination){
					ends[ends.length-1].push(buildingData[i].verts[j]);
				}
			}
		}
	}
	// Begin routing. Have a list of possible start and dest verts stored as [[c1,v1,v2,v3],[c2,v1,v2,v3],...]
	// since i may need to consider multiple start points, this will be ugly
	// preform full SPA on each entry to each dest. on finding of one entry to one dest save length, compare to next dest, store min.
	// compare that min against the min of each entry

	// scratch that. get a start point and just find the closest end. EZPZ
	var currentTotal = 999999999999999;
	var minE = 1;
	for (eblock in ends){
		for (e = 1; e < eblock.length; e++){
			resetMap(buildingData);
			start[0][1].dist = 0;
			var DJQ = createQ(buildingData);
			DJQ.sort(function(a,b){return a.dist-b.dist});
			while (DJQ.length != 0){
				var q = DJQ[0];
				var c = getCell(q,buildingData);
				if (q.xPos == eblock[e].xPos && q.yPos == eblock[e].yPos){
					break;
				}
				// have cell, so all verts in cell are neighbors.
				for (vert in c){
					if (DJQ.indexOf(vert) > -1){
						var ind = DJQ.indexOf(vert);
						var tempDist = q.dist + distance(q,DJQ[ind],c);
						if (tempDist < DJQ[ind].dist){
							DJQ[ind].dist = tempDist;
							DJQ[ind].prev = q;
						}
					}
				}
				//REMOVE Q IDK IF THIS SYNTAX IS CORRECT
				DJQ.remove(q);
				DJQ.sort(function(a,b){return a.dist-b.dist});
			}
			// can find distance
			var tempTotal = pathDistance(eblock[e]);
			if (tempTotal < currentTotal){
				currentTotal = tempTotal;
				minE = e;
			}
		}
	}
	//know closest enterance, finish her off
	resetMap(buildingData);
	var DJQ = createQ(buildingData);
	DJQ.sort(function(a,b){return a.dist-b.dist});
	while (DJQ.length != 0){
		var q = DJQ[0];
		var c = getCell(q,buildingData);
		if (q.xPos == eblock[minE].xPos && q.yPos == eblock[minE].yPos){
			break;
		}
		// have cell, so all verts in cell are neighbors.
		for (vert in c){
			if (DJQ.indexOf(vert) > -1){
				var ind = DJQ.indexOf(vert);
				var tempDist = q.dist + distance(q,DJQ[ind],c);
				if (tempDist < DJQ[ind].dist){
					DJQ[ind].dist = tempDist;
					DJQ[ind].prev = q;
				}
			}
		}
		DJQ.sort(function(a,b){return a.dist-b.dist});
	}

	return yieldRoute(eblock[minE]);


}



