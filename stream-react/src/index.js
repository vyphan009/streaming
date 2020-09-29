import React from 'react';
import ReactDOM from 'react-dom';
import 'bulma/css/bulma.min.css';
import './index.css';
import App from './App';
import Amplify, { Auth, Storage } from "aws-amplify";
import config from "./config";
import * as serviceWorker from './serviceWorker';

Amplify.configure({
  Auth: {
    mandatorySignIn: true,
    region: config.cognito.REGION,
    userPoolId: config.cognito.USER_POOL_ID,
    userPoolWebClientId: config.cognito.APP_CLIENT_ID
  },
  Storage: {
    AWSS3: {
        bucket: config.bucket.name, 
    }
}
});

ReactDOM.render(<App />, document.getElementById('root'));

serviceWorker.unregister();
