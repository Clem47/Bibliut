import React, { useState } from "react"
import { useNavigate } from "react-router-dom"
import "semantic-ui-css/semantic.min.css"
import { Button, Form, Grid, Header, Image, Segment } from "semantic-ui-react"

const LoginForm = () => {
  const [username, setUsername] = useState("")
  const [password, setPassword] = useState("")
  const [isLoggedIn, setIsLoggedIn] = useState(false)
  const navigate = useNavigate()

  const handleSubmit = async (event) => {
    event.preventDefault()
    try {
      const response = await fetch('http://185.212.226.162/api/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      });
      const data = await response.json();
      console.log(data.token);
      console.log(data);
      localStorage.setItem('token', data.token);
      localStorage.setItem('username', username);
      if (response.ok) {
        setIsLoggedIn(true)
      } else {
        console.error(data.detail)
      }
    } catch (error) {
      console.error(error)
    }
  }

  if (isLoggedIn) {
    navigate("/")
    window.location.reload()
  }

  return (
    <Grid textAlign="center" style={{ height: "90vh" }} verticalAlign="middle">
      <Grid.Column style={{ maxWidth: 450 }}>
        <Header as="h2" color="teal" textAlign="center">
          Log-in to your account
        </Header>
        <Form size="large" onSubmit={handleSubmit}>
          <Segment stacked>
            <Form.Input
              fluid
              icon="user"
              iconPosition="left"
              placeholder="User name"
              value={username}
              onChange={(event) => setUsername(event.target.value)}
            />
            <Form.Input
              fluid
              icon="lock"
              iconPosition="left"
              placeholder="Password"
              type="password"
              value={password}
              onChange={(event) => setPassword(event.target.value)}
            />
            <Button color="teal" fluid size="large" type="submit">
              Login
            </Button>
          </Segment>
        </Form>
      </Grid.Column>
    </Grid>
  )
}

export default LoginForm
