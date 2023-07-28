import React from 'react';
import {Link}  from "react-router-dom";
import 'semantic-ui-css/semantic.min.css';
import {Segment, Container, Grid, Header, List, Divider, Image, Icon} from 'semantic-ui-react';


const Footer = () => (
    
    <div>
    <Segment vertical style={{padding: '2em 0em', backgroundColor: '#F5F5F5', color: 'black'}}>
      <Container textAlign='center'>
        <Grid stackable>
          <Grid.Column width={8}>
            <Header as='h3' content='IUT - Bibliothèque' style={{color: '#009999'}}/>
            <List>
              <List.Item>15 rue Naudet - CS 10207</List.Item>
              <List.Item>33 175 Gradignan Cedex</List.Item>
              <List.Item>Tél. +33 (0)5 56 84 57 57</List.Item>
            </List>
          </Grid.Column>
          <Grid.Column width={8}>
            <Header as='h3' content='Liens' style={{color: '#009999'}} />
            <List link>
              <List.Item as='a' href='https://www.iut.u-bordeaux.fr/general/'>IUT de Bordeaux</List.Item>
              <List.Item as='a' href='http://bibliotheques.u-bordeaux.fr/Les-bibliotheques/Sciences-et-Technologies/Bibliotheque-de-l-IUT-de-Bordeaux'>Bilbiothèque de l'université de Bordeaux</List.Item>
              <List.Item as='a'>Nous contacter</List.Item>
            </List>
          </Grid.Column>
        </Grid>
        <div style={{paddingTop:'0.5em'}}>
            <a href='https://www.facebook.com/iutdebordeaux'><Icon name='facebook' size='big' color='blue' /></a>
            <a href='https://twitter.com/IUT_de_Bordeaux'><Icon name='twitter' size='big' color='blue' /></a>
            <a href='https://www.youtube.com/channel/UCaWP9UzIsK3H9NKx9AruRdA'><Icon name='youtube' size='big' color='red' /></a>
            <a href='https://www.instagram.com/iutdebordeaux/'><Icon name='instagram' size='big' color='black' /></a>
            <a href='https://www.linkedin.com/school/iut-de-bordeaux/'><Icon name='linkedin' size='big' color='blue' /></a>
        </div>
        <Divider section />
        
        <List horizontal divided link size='small'>
            <List.Item as='a' href='#'>
                Conditions générales d'utilisation
            </List.Item>
            <List.Item>
            Copyright 2020 IUT - Bibliothèque. Tous droits réservés.
            </List.Item>
        </List>
        </Container>
    </Segment>
    </div>
)

export default Footer