import React from 'react';
import { Toaster as Sonner, ToasterProps } from 'sonner';

export const Toaster = (props: ToasterProps) => <Sonner position="top-center" richColors {...props} />;
