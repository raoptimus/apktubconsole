db.AuthAssignments.find().forEach(function(d){ db.getSiblingDB('tubeadmins')['AuthAssignments'].insert(d); });
db.AuthItemChildren.find().forEach(function(d){ db.getSiblingDB('tubeadmins')['AuthItemChildren'].insert(d); });
db.AuthItems.find().forEach(function(d){ db.getSiblingDB('tubeadmins')['AuthItems'].insert(d); });
db.AuthRules.find().forEach(function(d){ db.getSiblingDB('tubeadmins')['AuthRules'].insert(d); });
db.AuthUsers.find().forEach(function(d){ db.getSiblingDB('tubeadmins')['AuthUsers'].insert(d); });