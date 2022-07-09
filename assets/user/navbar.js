import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import 'semantic-ui-css/semantic.min.css';
import '../styles/app.css';
import Navbar from './components/Navbar';

ReactDOM.render(<Navbar/>, document.querySelector('.navbar_user'));