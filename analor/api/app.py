from flask import Flask, request
import json
import psycopg2
from psycopg2.extras import RealDictCursor


app = Flask(__name__)

@app.route('/search')
def search():
    args = request.args
    parameter_dict = args.to_dict()['data']
    parameter_dict = json.loads(parameter_dict)
    conn = psycopg2.connect(database="new_db_analor",
                            user="postgres",
                            password="admin",
                            host="localhost", port="5432")
  

    cur = conn.cursor(cursor_factory=RealDictCursor)
    clauses = []
    whereclause = ''

    clauses.append(addColumnEqualValue('=','cas', parameter_dict['cas']))
    clauses.append(buildElementsWhereClause(parameter_dict['elementos']))
    clauses.append(buildPropsWhereClause(parameter_dict['propriedades']))
    
    firstClause = True
    for clause in clauses: 
        if not clause:
            continue
        if(not firstClause):
            whereclause += addAndConnector()
        whereclause += clause
        firstClause = False

    if whereclause:
        cur.execute(f'''SELECT * FROM univ1_210523 WHERE {whereclause};''')
    else:
        cur.execute(f'''SELECT * FROM univ1_210523 ;''')

    resultRows = cur.fetchall()
  
    formatedResultRows = formatRowsResult(resultRows)
    
    cur.close()
    conn.close()
    return formatedResultRows
  
def buildElementsWhereClause(elements):
    elementWhereClause = ''
    firstElement = True
    for elementObject in elements:
        if elementObject['quantidade'] != '':
            if not firstElement:
                elementWhereClause += addAndConnector()
            elementWhereClause += addColumnEqualValue('=',elementObject['nome'][0:4].lower(),elementObject['quantidade'])
            firstElement = False
        elif elementObject['tem']:
            if not firstElement:
                elementWhereClause += addAndConnector()
            elementWhereClause += addColumnEqualValue('>',elementObject['nome'][0:4].lower(),0)
            firstElement = False
    return elementWhereClause
        
def buildPropsWhereClause(properties):
    propertiesWhereClause = ''
    for propertie in properties:
        match propertie['nome'].lower():
            case 'peso molecular': 
                columnName = 'pmol'
            case 'ponto de fusão':
                columnName = 'pf'
            case 'ponto de ebulição':
                columnName = 'pe'
        if propertie['alcance'][0]:
            if propertiesWhereClause:
                propertiesWhereClause += addAndConnector()
            propertiesWhereClause += f'{columnName} >= {int(propertie["alcance"][0])}' 
            if propertie['alcance'][1]:
                propertiesWhereClause += addAndConnector()
                propertiesWhereClause += f'{columnName} <= {int(propertie["alcance"][1])}'
        elif propertie['alcance'][1]:
            if propertiesWhereClause:
                propertiesWhereClause += addAndConnector()
            propertiesWhereClause += f'{columnName} <= {int(propertie["alcance"][1])}'
    return propertiesWhereClause
            

def addColumnEqualValue(operator, colName, value) -> str:
    if value != '':
        return f"{colName} {operator} {value}"

def addAndConnector() -> str:
    return " AND "

def addOrConnector() -> str:
    return " OR "

def formatRowsResult(data):
    result = []
    for row in data:
        rowDictionary = dict(row)
        cleanedRowDictionary = { key:str(value).strip() for key, value in rowDictionary.items()}
        result.append(cleanedRowDictionary)
    return result

if __name__ == '__main__':
    app.run(host = 'localhost', port = 5000, debug = True)