from flask import Flask, render_template
from flask_sqlalchemy import SQLAlchemy
app = Flask(__name__)

@app.route('/')

def index():

    data = {
        'titulo': 'Sistema de Practica'
    }

    return render_template('index.html', data = data)

def pagina_error(error):
    return render_template('404.htmL'), 404

if __name__ == '__main__':
    app.register_error_handler(404, pagina_error)
    app.run(debug=True, port= 5000)