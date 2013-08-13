//TODO: 
//READ MAP, Read map data
//Score map
//entry point resolution
//path to destination
//move through floors ; next floor button
//output map

// Building data: 

//entryPoint info: starts as simple number

function main(entryPoint, goalCRN){
	// fuck this until the DB is set up
}

function indoor(entryPoint, destination){
	// Route from entrypoint to destination. Return image object
}

function scoreMap(mapFile){
	// Score the map. Should do this and have these saved to the DB
}

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
        //otherwise add in verts
        tCell.access.push(data[5]);
        tCell.entry.push(data[6]);
       	var tVert = new object;
       	tVert.vType = data[2];
       	tVert.xPos = data[3];
       	tVert.yPos = data[4];
       	tVert.access = data[5];
       	tVert.entry = data[6];
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


function planRoute(startPoint, destination, buildingData){
	// Startpoint is always an access point or a connector.
	var start = [];
	var ends = [];
	if (startPoint < 0){
		// if We're entering from the outside
		for (int i = 0; i < buildingData.length; i++){
			if (buildingData[i].entry == startPoint){
				start = buildingData[i];
				break;
			}
		}
	}
	else{
		// indoor starting point. will likely be routing to exit
		for (int i=0; i < buildingData.length; i++){
			if (buildingData[i].access == startPoint){
				start.push(buildingData[i]);
			}
		}
	}
	//have starting point as cell or array of cells. reslove end points
	for (int i=0; i <buildingData.length; i++){
		if (buildingData[i].access == destination){
			ends.push(buildingData[i]);
		}
	}
	// reslove nearest start & end
	
}


