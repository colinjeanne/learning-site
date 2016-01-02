import Constants from '../constants/constants';
import { createAction } from 'redux-actions';

export const signInUser = createAction(Constants.USER_SIGNIN);