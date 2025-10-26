import nigui
import jester
# import db_mysql

app.init()

routes:
  get "/":
    resp "API online."

  get "/ola":
    resp """{ "resposta": "Olá, Mundo!" }"""

runForever()

app.run()
