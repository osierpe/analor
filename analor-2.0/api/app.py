from flask import Flask, request
import json
from flask_cors import CORS
import google.cloud.logging
import logging
import os
from google.cloud.sql.connector import Connector
from unidecode import unidecode



app = Flask(__name__)
CORS(app, origins=["https://analor-front-0-0-1-41236692482.us-central1.run.app","https://analor.com.br"])
client = google.cloud.logging.Client()
client.setup_logging()
connector = Connector()

@app.route('/debug-env')
def debug_env():
    return {
        "PGHOST": os.getenv("PGHOST"),
        "PGSSLMODE": os.getenv("PGSSLMODE"),
    }

@app.route('/debug-socket')
def debug_socket():
    exists = os.path.exists('/cloudsql/handy-vortex-458519-u5:us-central1:analor-pg')
    return {'socket_exists': exists}

@app.route('/abrev')
def getAbrev():
    args = request.args
    conn = connector.connect(
        "handy-vortex-458519-u5:us-central1:analor-pg",
        "pg8000",
        user="analor-app",
        password="Z0TX-6}G.X$X*]D*",
        db="postgres"
    )

    cur = conn.cursor()
    cur.execute('SELECT NOME, NLIN FROM abrev;')

    formatedResultRows = formatRowsResult(cur)

    cur.close()
    conn.close()
    return formatedResultRows

@app.route('/search')
def search():
    unix_socket = '/cloudsql/{}'.format("handy-vortex-458519-u5:us-central1:analor-pg")
    args = request.args
    parameter_dict = args.to_dict()['data']
    parameter_dict = json.loads(parameter_dict)
    conn = conn = connector.connect(
        "handy-vortex-458519-u5:us-central1:analor-pg",
        "pg8000",
        user="analor-app",
        password="Z0TX-6}G.X$X*]D*",
        db="postgres"
    )
    logging.info(f'parameters received: {parameter_dict}')


    cur = conn.cursor()
    clauses = []
    whereclause = ''
    if parameter_dict['cas']:
        clauses.append(addColumnEqualValue('=','cas', f"\'{parameter_dict['cas']}\'"))
    clauses.append(buildElementsWhereClause(parameter_dict['elementos']))
    clauses.append(buildPropsWhereClause(parameter_dict['propriedades']))
    clauses.append(buildCarbonSkeletonWhereClause(parameter_dict['ecgf']))

    firstClause = True
    for clause in clauses: 
        if not clause:
            continue
        if(not firstClause):
            whereclause += addAndConnector()
        whereclause += clause
        firstClause = False
    logging.info(f'executing where clause: {whereclause}')
    if whereclause:
        cur.execute(f'''SELECT * FROM analor_0_0_1 WHERE {whereclause};''')
    else:
        cur.execute(f'''SELECT * FROM analor_0_0_1 ;''')

  
    formatedResultRows = formatRowsResult(cur)
    
    cur.close()
    conn.close()
    return formatedResultRows
  
def buildElementsWhereClause(elements):
    logging.info(f'elements: {elements}')

    elementWhereClause = ''
    firstElement = True
    for elementObject in elements:
        if elementObject['quantidade'] != '':
            if not firstElement:
                elementWhereClause += addAndConnector()
            elementWhereClause += addColumnEqualValue('=',unidecode(elementObject['nome'][0:4].lower()),elementObject['quantidade'])
            firstElement = False
        elif elementObject['tem'] == True:
            if not firstElement:
                elementWhereClause += addAndConnector()
            elementWhereClause += addColumnEqualValue('>',unidecode(elementObject['nome'][0:4].lower()),0)
            firstElement = False
        elif elementObject['tem'] == False:
            if not firstElement:
                elementWhereClause += addAndConnector()
            elementWhereClause += addColumnEqualValue('=',unidecode(elementObject['nome'][0:4].lower()),0)
            firstElement = False

    return elementWhereClause if len(elementWhereClause) else None
        
def buildCarbonSkeletonWhereClause(ecfgs):
    carbonSkeletonWhereClause = ''
    for ecfg in ecfgs:
        if ecfg['gFunc']:
            likeToMatch = ecfg["gFunc"].lower()
            match ecfg['inex']:
                case 'incSim':
                    if carbonSkeletonWhereClause:
                        carbonSkeletonWhereClause += addAndConnector() + f"LOWER(nlin) like '%{likeToMatch}%'"
                    else:
                        carbonSkeletonWhereClause += f"(LOWER(nlin) like '{likeToMatch}'"
                case 'excluir':
                    if carbonSkeletonWhereClause:
                        carbonSkeletonWhereClause += addAndConnector() + f"LOWER(nlin) not like '%{likeToMatch}%'"
                    else:
                        carbonSkeletonWhereClause += f"(LOWER(nlin) not like '%{likeToMatch}%'"
                case 'incluir':
                    if carbonSkeletonWhereClause:
                        carbonSkeletonWhereClause += addOrConnector() + f"LOWER(nlin) like '%{likeToMatch}%'"
                    else:
                        carbonSkeletonWhereClause += f"(LOWER(nlin) like '%{likeToMatch}%'"

    if carbonSkeletonWhereClause:
        carbonSkeletonWhereClause += ')'
    return carbonSkeletonWhereClause

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

def formatRowsResult(cursor):
    columns = [desc[0] for desc in cursor.description]
    result = []
    for row in cursor.fetchall():
        rowDictionary = dict(zip(columns, row))
        cleanedRowDictionary = { key: str(value).strip() if value is not None else "" for key, value in rowDictionary.items() }
        result.append(cleanedRowDictionary)
    return result