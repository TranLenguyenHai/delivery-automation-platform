import json
import uuid

with open('n8n-workflows/Điều phối kho Giao Hàng .json', 'r') as f:
    data = json.load(f)

# Nodes
schedule_node = {
    "parameters": {
        "rule": {
            "interval": [
                {
                    "field": "minutes",
                    "minutesInterval": 1
                }
            ]
        }
    },
    "type": "n8n-nodes-base.scheduleTrigger",
    "typeVersion": 1.3,
    "position": [-864, 550],
    "id": str(uuid.uuid4()),
    "name": "Schedule AI Scan"
}

get_delivering_node = {
    "parameters": {
        "url": "http://192.168.114.44:8000/api/orders/status/DELIVERING",
        "options": {}
    },
    "type": "n8n-nodes-base.httpRequest",
    "typeVersion": 4.4,
    "position": [-656, 550],
    "id": str(uuid.uuid4()),
    "name": "Get Delivering"
}

loop_node = {
    "parameters": {
        "options": {}
    },
    "type": "n8n-nodes-base.splitInBatches",
    "typeVersion": 3,
    "position": [-416, 550],
    "id": str(uuid.uuid4()),
    "name": "Loop Over Delivering Items"
}

ai_llm_node = {
    "parameters": {
        "promptType": "define",
        "text": "Phân tích trạng thái đơn hàng.",
        "messages": {
            "messageValues": [
                {
                    "message": "=Bạn là AI. Đơn hàng {{ $json.id }} đang giao. Hãy trả về JSON { \"status\": \"DELIVERED\" }."
                }
            ]
        }
    },
    "type": "@n8n/n8n-nodes-langchain.chainLlm",
    "typeVersion": 1.9,
    "position": [-200, 550],
    "id": str(uuid.uuid4()),
    "name": "AI Scan Chain"
}

groq_node = {
    "parameters": {
        "model": "llama-3.3-70b-versatile"
    },
    "type": "@n8n/n8n-nodes-langchain.lmChatGroq",
    "typeVersion": 1,
    "position": [-200, 750],
    "id": str(uuid.uuid4()),
    "name": "Groq Chat Model 2",
    "credentials": {
        "groqApi": {
            "id": "F0660oiHuE8wv0Cv",
            "name": "Groq account"
        }
    }
}

update_node = {
    "parameters": {
        "method": "PUT",
        "url": "=http://192.168.114.44:8000/api/orders/{{ $('Loop Over Delivering Items').item.json.id }}/status",
        "sendBody": True,
        "specifyBody": "json",
        "jsonBody": "{\n  \"status\": \"DELIVERED\"\n}",
        "options": {}
    },
    "type": "n8n-nodes-base.httpRequest",
    "typeVersion": 4.4,
    "position": [0, 550],
    "id": str(uuid.uuid4()),
    "name": "Update DELIVERED"
}

data['nodes'].extend([schedule_node, get_delivering_node, loop_node, ai_llm_node, groq_node, update_node])

# Connections
if "Schedule AI Scan" not in data['connections']:
    data['connections']["Schedule AI Scan"] = {"main": [[{"node": "Get Delivering", "type": "main", "index": 0}]]}
if "Get Delivering" not in data['connections']:
    data['connections']["Get Delivering"] = {"main": [[{"node": "Loop Over Delivering Items", "type": "main", "index": 0}]]}
if "Loop Over Delivering Items" not in data['connections']:
    data['connections']["Loop Over Delivering Items"] = {"main": [[], [{"node": "AI Scan Chain", "type": "main", "index": 0}]]}
if "AI Scan Chain" not in data['connections']:
    data['connections']["AI Scan Chain"] = {"main": [[{"node": "Update DELIVERED", "type": "main", "index": 0}]]}
if "Update DELIVERED" not in data['connections']:
    data['connections']["Update DELIVERED"] = {"main": [[{"node": "Loop Over Delivering Items", "type": "main", "index": 0}]]}

if "Groq Chat Model 2" not in data['connections']:
    data['connections']["Groq Chat Model 2"] = {"ai_languageModel": [[{"node": "AI Scan Chain", "type": "ai_languageModel", "index": 0}]]}

with open('n8n-workflows/Điều phối kho Giao Hàng .json', 'w') as f:
    json.dump(data, f, indent=2)

print("Workflow updated successfully.")
