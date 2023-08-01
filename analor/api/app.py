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
    conn = psycopg2.connect(database="analor",
                            user="postgres",
                            password="admin",
                            host="localhost", port="5432")
  

    cur = conn.cursor(cursor_factory=RealDictCursor)
    clauses = []
    whereclause = ''

    clauses.append(addColumnEqualValue('=','cas', parameter_dict['cas']))

    clauses.append(buildElementsWhereClause(parameter_dict['elementos']))

    for clause in clauses:
        firstClause = True
        if not clause:
            continue
        if(not firstClause):
            whereclause += addAndConnector()
        whereclause += clause
        firstClause = False

    if whereclause:
        cur.execute(f'''SELECT * FROM UNIV1_teste WHERE {whereclause};''')
    else:
        cur.execute(f'''SELECT * FROM UNIV1_teste;''')

    resultRows = cur.fetchall()
  
    formatedResultRows = formatRowsResult(resultRows)
    
    cur.close()
    conn.close()
    return formatedResultRows
  
def buildElementsWhereClause(elements):
    casWhereClause = ''
    firstElement = True
    for elementObject in elements:
        if elementObject['quantidade'] != '':
            if not firstElement:
                casWhereClause += addAndConnector()
            casWhereClause += addColumnEqualValue('=',elementObject['nome'][0:4].lower(),elementObject['quantidade'])
            firstElement = False
        elif elementObject['tem']:
            if not firstElement:
                casWhereClause += addAndConnector()
            casWhereClause += addColumnEqualValue('>',elementObject['nome'][0:4].lower(),0)
            firstElement = False
    return casWhereClause
        

def addColumnEqualValue(operator, colName, value) -> str:
    if value:
        return f"{colName} {operator} '{value}'"

def addAndConnector() -> str:
    return " AND "

def addOrConnector() -> str:
    return " OR "



def addPmolClause(whereclause, minPmol, maxPmol):
    return 0

def formatRowsResult(data):
    result = []
    for row in data:
        rowDictionary = dict(row)
        print(row)
        print(rowDictionary)
        cleanedRowDictionary = { key:str(value).strip() for key, value in rowDictionary.items()}
        result.append(cleanedRowDictionary)
    return result

if __name__ == '__main__':
    app.run(host = 'localhost', port = 5000, debug = True)