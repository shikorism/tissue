import * as React from 'react';
import * as ReactDOM from 'react-dom';
import { CheckinForm } from './components/CheckinForm';

const initialState = JSON.parse(document.getElementById('initialState')?.textContent as string);
ReactDOM.render(<CheckinForm initialState={initialState}/>, document.getElementById('checkinForm'));
