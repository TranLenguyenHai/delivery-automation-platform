import json
import uuid

with open('n8n-workflows/Điều phối kho Giao Hàng .json', 'r') as f:
    data = json.load(f)

code_node = {
    "parameters": {
        "jsCode": "let aiData = {};\ntry {\n  aiData = JSON.parse($input.item.json.text);\n} catch(e) {\n  aiData = {status: 'DELIVERED'};\n}\nreturn aiData;"
    },
    "type": "n8n-nodes-base.code",
    "typeVersion": 2,
    "position": [0, 350],
    "id": str(uuid.uuid4()),
    "name": "Parse AI Output"
}

data['nodes'].append(code_node)

# Fix connection from AI Scan Chain to Parse AI Output, then Parse AI Output to Update DELIVERED
data['connections']['AI Scan Chain'] = {"main": [[{"node": "Parse AI Output", "type": "main", "index": 0}]]}
data['connections']['Parse AI Output'] = {"main": [[{"node": "Update DELIVERED", "type": "main", "index": 0}]]}

# Update position of nodes to make them visible and linear
for node in data['nodes']:
    if node['name'] == 'Schedule AI Scan':
        node['position'] = [-864, 550]
    elif node['name'] == 'Get Delivering':
        node['position'] = [-650, 550]
    elif node['name'] == 'Loop Over Delivering Items':
        node['position'] = [-400, 550]
    elif node['name'] == 'AI Scan Chain':
        node['position'] = [-150, 550]
    elif node['name'] == 'Groq Chat Model 2':
        node['position'] = [-150, 750]
    elif node['name'] == 'Update DELIVERED':
        node['position'] = [350, 550]

with open('n8n-workflows/Điều phối kho Giao Hàng .json', 'w') as f:
    json.dump(data, f, indent=2)

print("Added Code node and updated layout.")
